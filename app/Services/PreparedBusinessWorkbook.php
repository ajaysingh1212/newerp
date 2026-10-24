<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class PreparedBusinessWorkbook
{
    public function read(string $path): array
    {
        try {
            $reader = new Xlsx;
            $reader->setReadDataOnly(true);
            $reader->setLoadSheetsOnly(['_ImportPayload']);
            $workbook = $reader->load($path);
            $sheet = $workbook->getSheetByName('_ImportPayload');
            if (! $sheet || $sheet->getCell('B1')->getValue() !== 'BUSINESS_MASTER_READY_V1') {
                throw new \RuntimeException('Not a ready import workbook.');
            }
            $count = (int) $sheet->getCell('B3')->getValue();
            if ($count < 1 || $count > 1000) {
                throw new \RuntimeException('Invalid package size.');
            }
            $encoded = '';
            for ($index = 0; $index < $count; $index++) {
                if ((int) $sheet->getCell('A'.($index + 6))->getValue() !== $index + 1) {
                    throw new \RuntimeException('Package chunks are incomplete.');
                }
                $encoded .= (string) $sheet->getCell('B'.($index + 6))->getValue();
            }
            $compressed = base64_decode($encoded, true);
            $json = $compressed === false ? false : gzdecode($compressed, 64 * 1024 * 1024);
            if ($json === false || hash('sha256', $json) !== $sheet->getCell('B2')->getValue()) {
                throw new \RuntimeException('Package integrity check failed.');
            }
            $manifest = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            $workbook->disconnectWorksheets();
            if (count($manifest['rows'] ?? []) > 100000 || count($manifest['parties'] ?? []) > 5000
                || ! preg_match('/^[a-f0-9]{64}$/', $manifest['sha256'] ?? '')
                || ! is_array($manifest['current_source_indexes'] ?? null)) {
                throw new \RuntimeException('Invalid manifest structure.');
            }
            foreach ($manifest['current_source_indexes'] as $index) {
                if (! is_int($index) || ! isset($manifest['rows'][$index]['normalized']['status'])) {
                    throw new \RuntimeException('Invalid current-device row.');
                }
            }
            $manifest['_package_sha256'] = hash_file('sha256', $path);

            return $manifest;
        } catch (\Throwable $exception) {
            throw ValidationException::withMessages(['ready_file' => 'Unable to read the ready workbook: '.$exception->getMessage()]);
        }
    }
}
