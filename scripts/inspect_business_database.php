<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$result = [];
foreach (['manual_parties', 'manual_products', 'business_devices'] as $table) {
    $result['schema'][$table] = Illuminate\Support\Facades\Schema::getColumns($table);
    echo $table.' '.Illuminate\Support\Facades\DB::table($table)->count().' rows'.PHP_EOL;
    foreach ($result['schema'][$table] as $column) {
        echo $column['name'].' '.$column['type'].' '.($column['nullable'] ? 'nullable' : 'required').PHP_EOL;
    }
}
$result['parties'] = App\Models\ManualParty::withTrashed()->get(['id', 'name', 'state', 'district', 'city', 'deleted_at'])->toArray();
$result['products'] = App\Models\ManualProduct::withTrashed()->get(['id', 'name', 'deleted_at'])->toArray();
file_put_contents(storage_path('app/private/master-report/database-before.json'), json_encode($result, JSON_PRETTY_PRINT));
$columns = Illuminate\Support\Facades\Schema::getColumnListing('manual_activations');
echo 'ACTIVATION COLUMNS '.json_encode($columns).PHP_EOL;
$selected = array_values(array_intersect(['manual_party_id', 'manual_product_id', 'imei', 'imei_number', 'vts_number', 'sim_number'], $columns));
if (in_array('manual_party_id', $selected)) {
    file_put_contents(storage_path('app/private/master-report/existing-activations.json'), json_encode(Illuminate\Support\Facades\DB::table('manual_activations')->whereNull('deleted_at')->get($selected)));
}
