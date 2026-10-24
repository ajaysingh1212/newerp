<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['session.driver' => 'array']);
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$user = App\Models\User::findOrFail(1);
$deviceId = Illuminate\Support\Facades\DB::table('business_devices')->whereNotNull('business_import_id')->value('id');
$rowId = Illuminate\Support\Facades\DB::table('business_import_rows')->where('kind', 'sale')->value('id');
$paths = [
    '/admin/business-management', '/admin/business-management/report', '/admin/business-management/import',
    '/admin/business-management/'.$deviceId, '/admin/business-imports', '/admin/business-imports/rows/'.$rowId,
];
foreach ($paths as $index => $path) {
    Illuminate\Support\Facades\Auth::setUser($user);
    $response = $kernel->handle(Illuminate\Http\Request::create($path));
    echo $path.' '.$response->getStatusCode().' '.strlen($response->getContent()).' bytes'.PHP_EOL;
    if ($response->getStatusCode() !== 200) {
        exit(1);
    }
    preg_match_all('/<script(?:\s[^>]*)?>(.*?)<\/script>/s', $response->getContent(), $scripts);
    file_put_contents(storage_path('app/private/master-report/smoke-'.$index.'.js'), implode(PHP_EOL, $scripts[1]));
}
