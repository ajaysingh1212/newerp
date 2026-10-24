<?php

namespace App\Services;

use App\Models\BusinessDevice;
use App\Models\BusinessImport;
use App\Models\ManualParty;
use App\Models\ManualProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class MasterReportImporter
{
    public static function nameKey(string $name): string
    {
        return preg_replace('/[^\pL\pN]/u', '', mb_strtolower(trim($name)));
    }

    public function import(array $manifest, string $sourcePath, int $userId, bool $prepared = false, ?string $fileName = null): BusinessImport
    {
        $fileSha = hash_file('sha256', $sourcePath);
        $sha = $manifest['sha256'];
        if ($fileSha !== ($prepared ? ($manifest['_package_sha256'] ?? '') : $sha)) {
            throw new RuntimeException('Source workbook does not match the prepared manifest.');
        }
        if ($existing = BusinessImport::where('sha256', $sha)->first()) {
            return $existing;
        }
        $rows = $manifest['rows'];
        if (count($rows) !== $manifest['summary']['source_rows'] || array_sum(array_map(fn ($row) => count($row['source_cells']), $rows)) !== $manifest['summary']['source_cells']) {
            throw new RuntimeException('Source row or cell reconciliation failed before import.');
        }
        $archivePath = 'private/business-imports/'.$sha.'.xlsx';
        $stream = fopen($sourcePath, 'rb');
        Storage::disk('local')->put($archivePath, $stream);
        fclose($stream);
        if (hash_file('sha256', Storage::disk('local')->path($archivePath)) !== $fileSha) {
            throw new RuntimeException('Archived workbook verification failed.');
        }

        return DB::transaction(function () use ($manifest, $rows, $sha, $archivePath, $sourcePath, $userId, $prepared, $fileName, $fileSha) {
            $summary = $manifest['summary'];
            $summary['parties_created'] = 0;
            $summary['parties_reused'] = 0;
            $summary['products_created'] = 0;
            $summary['archive_sha256'] = $fileSha;
            $summary['live_conflicts'] = 0;
            $summary['existing_devices_reused'] = 0;
            $partyMap = [];
            $parties = ManualParty::withTrashed()->get();
            foreach ($manifest['parties'] as $sourceParty) {
                $expected = $sourceParty['match_name'] ?? $sourceParty['name'];
                $keys = $prepared ? [self::nameKey($expected), self::nameKey($sourceParty['name'])] : [self::nameKey($expected)];
                $matches = $parties->filter(fn ($party) => in_array(self::nameKey($party->name), $keys, true));
                if ($matches->count() > 1) {
                    if ($prepared) {
                        $partyMap[$sourceParty['sheet']] = null;
                        $summary['live_conflicts']++;

                        continue;
                    }
                    throw new RuntimeException('Ambiguous party match: '.$expected);
                }
                if ($matches->count() === 1) {
                    $party = $matches->first();
                    // Reuse the identity even if archived; never create a duplicate.
                    $summary['parties_reused']++;
                } else {
                    if ($sourceParty['match_name'] && ! $prepared) {
                        throw new RuntimeException('Reviewed party match no longer exists: '.$expected);
                    }
                    $party = ManualParty::create([
                        'name' => $sourceParty['name'], 'phone' => null, 'state' => $sourceParty['state'],
                        'district' => $sourceParty['district'], 'city' => $sourceParty['city'],
                        'address' => $sourceParty['address'], 'created_by' => $userId,
                    ]);
                    $parties->push($party);
                    $summary['parties_created']++;
                }
                $partyMap[$sourceParty['sheet']] = $party->id;
            }
            $products = ManualProduct::withTrashed()->get();
            $productMap = [];
            foreach ($rows as $row) {
                $name = $row['normalized']['product_name'] ?? null;
                if (! $name || isset($productMap[$name])) {
                    continue;
                }
                $key = self::nameKey($name);
                $aliases = ['bikzo' => 'etbikzo', 'express' => 'etexpress', '3xpro' => 'et3xpro', 'basic' => 'etbasic', 'obd' => 'etobd'];
                $key = $aliases[$key] ?? $key;
                $matches = $products->filter(function ($product) use ($aliases, $key) {
                    $productKey = self::nameKey($product->name);

                    return ($aliases[$productKey] ?? $productKey) === $key;
                })->sortBy(fn ($product) => $product->trashed() ? 1 : 0);
                if ($matches->isEmpty()) {
                    $product = ManualProduct::create(['name' => $name, 'description' => 'Imported from master report; original model labels retained in source ledger.']);
                    $products->push($product);
                    $summary['products_created']++;
                } else {
                    $product = $matches->first();
                }
                $productMap[$name] = $product->id;
            }
            $batch = BusinessImport::create(['file_name' => basename($fileName ?? $sourcePath), 'sha256' => $sha, 'archive_path' => $archivePath, 'summary' => $summary, 'created_by' => $userId]);
            $deviceRows = [];
            $existingDeviceMap = [];
            $liveIssues = [];
            $now = now()->format('Y-m-d H:i:s');
            foreach ($manifest['current_source_indexes'] as $index) {
                $row = $rows[$index];
                $value = $row['normalized'];
                if ($prepared && ! ($partyMap[$row['sheet']] ?? null)) {
                    $liveIssues[$row['identity']] = 'Multiple matching parties in live database; device assignment requires review.';

                    continue;
                }
                // A repeated workbook is a no-op. Existing operational devices are
                // never silently overwritten by an unrelated historical import.
                $exists = $value['vts_number'] ? BusinessDevice::where('vts_number', $value['vts_number'])->first() : BusinessDevice::where('imei_number', $value['imei_number'])->first();
                if ($exists) {
                    if ($prepared) {
                        $existingDeviceMap[$row['identity']] = $exists->id;
                        if ($exists->manual_party_id !== $partyMap[$row['sheet']]) {
                            $liveIssues[$row['identity']] = 'Device already belongs to a different party in live database; original assignment retained.';
                            $summary['live_conflicts']++;
                        } else {
                            $summary['existing_devices_reused']++;
                        }

                        continue;
                    }
                    throw new RuntimeException('Device already exists outside this import: '.($value['vts_number'] ?? $value['imei_number']));
                }
                $deviceRows[] = [
                    'manual_party_id' => $partyMap[$row['sheet']], 'manual_product_id' => $productMap[$value['product_name']] ?? null,
                    'vts_number' => $value['vts_number'], 'imei_number' => $value['imei_number'], 'sim_number' => null,
                    'sales_price' => $value['sales_price'], 'assigned_at' => $value['assigned_at'], 'status' => $value['status'],
                    'created_by' => $userId, 'activated_by' => null, 'activated_at' => $value['activated_at'],
                    'business_import_id' => $batch->id, 'created_at' => $now, 'updated_at' => $now,
                ];
            }
            foreach (array_chunk($deviceRows, 300) as $chunk) {
                DB::table('business_devices')->insert($chunk);
            }
            $deviceMap = $existingDeviceMap;
            foreach (DB::table('business_devices')->where('business_import_id', $batch->id)->get(['id', 'vts_number', 'imei_number']) as $device) {
                $identity = $device->vts_number ? 'vts:'.$device->vts_number : 'imei:'.$device->imei_number;
                $deviceMap[$identity] = $device->id;
            }
            $chunk = [];
            foreach ($rows as $row) {
                $normalized = $row['normalized'];
                if (isset($liveIssues[$row['identity'] ?? ''])) {
                    $row['issues'][] = $liveIssues[$row['identity']];
                }
                if ($prepared && array_key_exists($row['sheet'], $partyMap) && $partyMap[$row['sheet']] === null) {
                    $row['issues'][] = 'Multiple matching parties in live database; original party label preserved for review.';
                }
                if (isset($normalized['product_name'])) {
                    $normalized['manual_product_id'] = $productMap[$normalized['product_name']] ?? null;
                }
                $review = count(array_filter($row['issues'], fn ($issue) => ! str_starts_with($issue, 'Repeated device history'))) > 0;
                $chunk[] = [
                    'business_import_id' => $batch->id, 'sheet' => $row['sheet'], 'row_number' => $row['row_number'],
                    'manual_party_id' => $partyMap[$row['sheet']] ?? null, 'business_device_id' => $deviceMap[$row['identity'] ?? ''] ?? null,
                    'kind' => $row['kind'], 'vts_number' => $normalized['vts_number'] ?? null,
                    'source_cells' => json_encode($row['source_cells'], JSON_THROW_ON_ERROR),
                    'normalized' => json_encode($normalized, JSON_THROW_ON_ERROR), 'issues' => json_encode($row['issues'], JSON_THROW_ON_ERROR), 'needs_review' => $review,
                ];
                if (count($chunk) === 200) {
                    DB::table('business_import_rows')->insert($chunk);
                    $chunk = [];
                }
            }
            if ($chunk) {
                DB::table('business_import_rows')->insert($chunk);
            }
            if ($prepared) {
                $summary['source_current_devices'] = $summary['current_devices'];
                $summary['current_devices'] = count($deviceRows);
                $summary['review_rows'] = DB::table('business_import_rows')->where('business_import_id', $batch->id)->where('needs_review', true)->count();
                $batch->update(['summary' => $summary]);
            }
            if (DB::table('business_import_rows')->where('business_import_id', $batch->id)->count() !== count($rows)
                || DB::table('business_devices')->where('business_import_id', $batch->id)->count() !== count($deviceRows)) {
                throw new RuntimeException('Database reconciliation failed. Entire import has been rolled back.');
            }

            return $batch;
        });
    }
}
