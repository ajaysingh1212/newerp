<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config([
    'database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
    'filesystems.disks.local.root' => storage_path('app/private/master-report/ready-import-test'),
]);
Illuminate\Support\Facades\DB::purge('sqlite');
Illuminate\Support\Facades\Storage::forgetDisk('local');
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Schema::create('users', function (Blueprint $table) { $table->id(); $table->string('name'); });
Illuminate\Support\Facades\DB::table('users')->insert(['id' => 1, 'name' => 'Import test']);
Schema::create('manual_parties', function (Blueprint $table) {
    $table->id(); $table->string('name'); $table->string('phone');
    foreach (['state', 'district', 'city', 'address'] as $field) { $table->text($field)->nullable(); }
    $table->unsignedBigInteger('created_by')->nullable(); $table->timestamps(); $table->softDeletes();
});
Schema::create('manual_products', function (Blueprint $table) { $table->id(); $table->string('name'); $table->text('description')->nullable(); $table->timestamps(); $table->softDeletes(); });
(require database_path('migrations/2026_10_24_000001_create_business_devices_table.php'))->up();
(require database_path('migrations/2026_10_24_000002_add_business_import_ledger.php'))->up();
$path = base_path('outputs/business-live-import-20261024/LIVE_IMPORT.xlsx');
$manifest = app(App\Services\PreparedBusinessWorkbook::class)->read($path);
$batch = app(App\Services\MasterReportImporter::class)->import($manifest, $path, 1, true, 'LIVE_IMPORT.xlsx');
$counts = [
    'database' => 'isolated in-memory SQLite',
    'devices' => App\Models\BusinessDevice::count(), 'parties' => App\Models\ManualParty::count(),
    'source_rows' => App\Models\BusinessImportRow::count(), 'batch' => $batch->id,
];
if ($counts['devices'] !== 7516 || $counts['parties'] !== 91 || $counts['source_rows'] !== 21260) {
    throw new RuntimeException('Full ready workbook import failed reconciliation.');
}
app(App\Services\MasterReportImporter::class)->import($manifest, $path, 1, true, 'LIVE_IMPORT.xlsx');
if (App\Models\BusinessDevice::count() !== 7516 || App\Models\BusinessImportRow::count() !== 21260) {
    throw new RuntimeException('Repeated ready upload created duplicates.');
}
file_put_contents(base_path('outputs/business-live-import-20261024/import-test.json'), json_encode($counts, JSON_PRETTY_PRINT));
echo json_encode($counts, JSON_PRETTY_PRINT).PHP_EOL;
