<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$batch = App\Models\BusinessImport::findOrFail(1);
$rows = Illuminate\Support\Facades\DB::table('business_import_rows')->where('business_import_id', $batch->id)->get()->map(function ($row) {
    $row->source_cells = json_decode($row->source_cells, true);
    $row->normalized = json_decode($row->normalized, true);
    $row->issues = json_decode($row->issues, true);
    return $row;
});
$result = [
    'batch' => $batch->toArray(),
    'archive_sha256' => hash_file('sha256', Illuminate\Support\Facades\Storage::disk('local')->path($batch->archive_path)),
    'rows' => $rows,
    'devices' => Illuminate\Support\Facades\DB::table('business_devices')->where('business_import_id', $batch->id)->get(),
    'parties' => Illuminate\Support\Facades\DB::table('manual_parties')->get(['id', 'name', 'deleted_at']),
];
file_put_contents(storage_path('app/private/master-report/database-after.json'), json_encode($result, JSON_THROW_ON_ERROR));
echo 'Database snapshot saved for independent verification.'.PHP_EOL;
