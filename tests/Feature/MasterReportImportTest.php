<?php

namespace Tests\Feature;

use App\Models\BusinessDevice;
use App\Models\BusinessImportRow;
use App\Models\ManualParty;
use App\Services\MasterReportImporter;
use App\Services\PreparedBusinessWorkbook;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;

class MasterReportImportTest extends BusinessManagementTest
{
    private function fixture(): array
    {
        Storage::fake('local');
        $path = tempnam(sys_get_temp_dir(), 'master-report-');
        file_put_contents($path, 'An immutable workbook fixture');
        $values = ['product_name' => 'Tracker', 'vts_number' => '000123', 'imei_number' => '123456789012345', 'sales_price' => null, 'assigned_at' => null, 'status' => 'activated', 'activated_at' => null];
        $cell = ['value' => '000123', 'formula' => null, 'type' => 's', 'style' => '1'];
        $manifest = [
            'sha256' => hash_file('sha256', $path),
            'summary' => ['sheets' => 2, 'source_rows' => 3, 'source_cells' => 3, 'current_devices' => 1, 'conflicting_devices' => 0],
            'parties' => [
                ['sheet' => 'Old', 'name' => 'PARTY A', 'match_name' => null, 'state' => null, 'district' => null, 'city' => null, 'address' => null],
                ['sheet' => 'New', 'name' => 'New Party', 'match_name' => null, 'state' => null, 'district' => null, 'city' => null, 'address' => null],
            ],
            'rows' => [
                ['sheet' => 'Old', 'row_number' => 7, 'kind' => 'sale', 'identity' => 'vts:000123', 'source_cells' => ['A7' => $cell], 'normalized' => array_merge($values, ['assigned_at' => '2025-01-01']), 'issues' => []],
                ['sheet' => 'New', 'row_number' => 8, 'kind' => 'sale', 'identity' => 'vts:000123', 'source_cells' => ['A8' => $cell], 'normalized' => $values, 'issues' => ['Sales value unavailable.']],
                ['sheet' => 'Old', 'row_number' => 5, 'kind' => 'control', 'source_cells' => ['A5' => ['value' => '2', 'formula' => 'SUM(A7:A8)', 'type' => null, 'style' => '0']], 'normalized' => [], 'issues' => []],
            ],
            'current_source_indexes' => [1],
        ];

        return [$path, $manifest];
    }

    public function test_import_matches_parties_preserves_all_source_rows_and_is_idempotent(): void
    {
        [$path, $manifest] = $this->fixture();
        try {
            $batch = app(MasterReportImporter::class)->import($manifest, $path, 1);
            $this->assertSame(1, $batch->summary['parties_created']);
            $this->assertSame(1, $batch->summary['parties_reused']);
            $this->assertDatabaseCount('manual_parties', 2);
            $this->assertDatabaseCount('business_import_rows', 3);
            $this->assertDatabaseCount('business_devices', 1);
            $device = BusinessDevice::firstOrFail();
            $this->assertSame(ManualParty::where('name', 'New Party')->firstOrFail()->id, $device->manual_party_id);
            $this->assertNull($device->sales_price);
            $this->assertNull($device->assigned_at);
            $this->assertNull($device->activated_by);
            $this->assertSame('activated', $device->status);
            $this->assertSame('000123', BusinessImportRow::where('sheet', 'Old')->where('kind', 'sale')->firstOrFail()->source_cells['A7']['value']);
            $this->assertSame(2, BusinessImportRow::where('business_device_id', $device->id)->count());
            $again = app(MasterReportImporter::class)->import($manifest, $path, 1);
            $this->assertSame($batch->id, $again->id);
            $this->assertDatabaseCount('manual_parties', 2);
            $this->assertDatabaseCount('business_import_rows', 3);
            $this->get(route('admin.business-management.show', $device))->assertOk()->assertSee('Not supplied in source');
            $this->get(route('admin.business-management.report'))->assertOk();
            $this->get(route('admin.business-imports.index'))->assertOk();
            $row = BusinessImportRow::where('kind', 'control')->firstOrFail();
            $this->get(route('admin.business-imports.show', $row))->assertOk()->assertSee('SUM(A7:A8)');
            $this->getJson(route('admin.business-imports.index', ['review' => '1', 'draw' => 1, 'length' => 10]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk()->assertJsonPath('recordsTotal', 1);
            $this->get(route('admin.business-imports.download', $batch))->assertOk();
            Gate::define('business_management_report', fn () => false);
            $this->get(route('admin.business-imports.index'))->assertForbidden();
            $this->get(route('admin.business-imports.show', $row))->assertForbidden();
            $this->get(route('admin.business-imports.download', $batch))->assertForbidden();
        } finally {
            unlink($path);
        }
    }

    public function test_missing_reviewed_party_rolls_back_all_new_records(): void
    {
        [$path, $manifest] = $this->fixture();
        $manifest['parties'][0]['name'] = 'Created before later failure';
        $manifest['parties'][1]['match_name'] = 'No such reviewed match';
        try {
            app(MasterReportImporter::class)->import($manifest, $path, 1);
            $this->fail('Expected reviewed match to fail.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('no longer exists', $exception->getMessage());
            $this->assertDatabaseCount('business_imports', 0);
            $this->assertDatabaseCount('business_import_rows', 0);
            $this->assertDatabaseCount('business_devices', 0);
            $this->assertDatabaseCount('manual_parties', 1);
        } finally {
            unlink($path);
        }
    }

    public function test_ready_import_continues_past_existing_device_conflict_and_creates_missing_party(): void
    {
        [$path, $manifest] = $this->fixture();
        $manifest['_package_sha256'] = hash_file('sha256', $path);
        $manifest['parties'][1]['match_name'] = 'An alias absent from this live database';
        $row = $manifest['rows'][0];
        $row['row_number'] = 9;
        $row['source_cells'] = ['A9' => ['value' => '000456', 'formula' => null, 'type' => 's', 'style' => '1']];
        $row['identity'] = 'vts:000456';
        $row['normalized']['vts_number'] = '000456';
        $manifest['rows'][] = $row;
        $manifest['summary']['source_rows'] = 4;
        $manifest['summary']['source_cells'] = 4;
        $manifest['summary']['current_devices'] = 2;
        $manifest['current_source_indexes'] = [1, 3];
        $existing = BusinessDevice::create(['manual_party_id' => 1, 'manual_product_id' => 1, 'vts_number' => '000123', 'sim_number' => 'existing', 'sales_price' => 123, 'assigned_at' => '2026-10-24']);
        try {
            $batch = app(MasterReportImporter::class)->import($manifest, $path, 1, true, 'LIVE_IMPORT.xlsx');
            $this->assertSame('LIVE_IMPORT.xlsx', $batch->file_name);
            $this->assertSame(1, $batch->summary['current_devices']);
            $this->assertSame(1, $batch->summary['live_conflicts']);
            $this->assertDatabaseCount('business_devices', 2);
            $this->assertSame(1, $existing->fresh()->manual_party_id);
            $this->assertSame('123.00', $existing->fresh()->sales_price);
            $this->assertDatabaseHas('manual_parties', ['name' => 'New Party']);
            $this->assertDatabaseCount('business_import_rows', 4);
            $this->assertDatabaseHas('business_import_rows', ['sheet' => 'New', 'needs_review' => true, 'business_device_id' => $existing->id]);
            app(MasterReportImporter::class)->import($manifest, $path, 1, true);
            $this->assertDatabaseCount('business_devices', 2);
        } finally {
            unlink($path);
        }
    }

    public function test_ready_workbook_reader_verifies_payload_and_checksum(): void
    {
        [$path, $manifest] = $this->fixture();
        $book = new Spreadsheet;
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('_ImportPayload');
        $json = json_encode($manifest);
        $encoded = base64_encode(gzencode($json));
        $sheet->fromArray([['format', 'BUSINESS_MASTER_READY_V1'], ['manifest_sha256', hash('sha256', $json)], ['chunk_count', 1]]);
        $sheet->setCellValue('A6', 1);
        $sheet->setCellValue('B6', $encoded);
        try {
            (new Xlsx($book))->save($path);
            $read = app(PreparedBusinessWorkbook::class)->read($path);
            $this->assertSame(3, count($read['rows']));
            $this->assertSame(hash_file('sha256', $path), $read['_package_sha256']);
            $sheet->setCellValue('B2', str_repeat('0', 64));
            (new Xlsx($book))->save($path);
            $this->expectException(ValidationException::class);
            app(PreparedBusinessWorkbook::class)->read($path);
        } finally {
            unlink($path);
        }
    }

    public function test_ready_workbook_upload_needs_no_mapping_and_checks_import_permission(): void
    {
        [$path, $manifest] = $this->fixture();
        $book = new Spreadsheet;
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('_ImportPayload');
        $json = json_encode($manifest);
        $sheet->fromArray([['format', 'BUSINESS_MASTER_READY_V1'], ['manifest_sha256', hash('sha256', $json)], ['chunk_count', 1]]);
        $sheet->setCellValue('A6', 1);
        $sheet->setCellValue('B6', base64_encode(gzencode($json)));
        try {
            (new Xlsx($book))->save($path);
            $upload = fn () => UploadedFile::fake()->createWithContent('LIVE_IMPORT.xlsx', file_get_contents($path));
            $this->postJson(route('admin.business-management.readyPreview'), ['ready_file' => $upload()])
                ->assertOk()->assertJsonPath('devices', 1)->assertJsonPath('source_rows', 3);
            $this->post(route('admin.business-management.readyImport'), ['ready_file' => $upload()])->assertRedirect();
            $this->assertDatabaseCount('business_devices', 1);
            $this->assertDatabaseCount('business_import_rows', 3);
            Gate::define('business_management_import', fn () => false);
            $this->postJson(route('admin.business-management.readyPreview'), ['ready_file' => $upload()])->assertForbidden();
            $this->post(route('admin.business-management.readyImport'), ['ready_file' => $upload()])->assertForbidden();
        } finally {
            unlink($path);
        }
    }
}
