<?php
Use App\Http\Controllers\Admin\StockTransferController;
Use App\Http\Controllers\Admin\CheckComplainController;
Use App\Http\Controllers\Admin\ActivationRequestController;
Use App\Http\Controllers\Admin\RechargeRequestController;
Use App\Http\Controllers\Admin\CheckPartyStockController;
Use App\Http\Controllers\Admin\UnbindProductController;

use App\Http\Controllers\Admin\CurrentStockController;
use App\Http\Controllers\Admin\UserAlertsControlle;
use App\Http\Controllers\Admin\KycRechargeController;
use App\Http\Controllers\Admin\VehicleSharingController;
use App\Http\Controllers\Admin\DeleteDataController;


Route::redirect('/', '/login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Auth::routes();

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::post('permissions/parse-csv-import', 'PermissionsController@parseCsvImport')->name('permissions.parseCsvImport');
    Route::post('permissions/process-csv-import', 'PermissionsController@processCsvImport')->name('permissions.processCsvImport');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::post('roles/parse-csv-import', 'RolesController@parseCsvImport')->name('roles.parseCsvImport');
    Route::post('roles/process-csv-import', 'RolesController@processCsvImport')->name('roles.processCsvImport');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::post('users/media', 'UsersController@storeMedia')->name('users.storeMedia');
    Route::post('users/ckmedia', 'UsersController@storeCKEditorImages')->name('users.storeCKEditorImages');
    Route::post('users/parse-csv-import', 'UsersController@parseCsvImport')->name('users.parseCsvImport');
    Route::post('users/process-csv-import', 'UsersController@processCsvImport')->name('users.processCsvImport');
    Route::resource('users', 'UsersController');

    // Team
    Route::delete('teams/destroy', 'TeamController@massDestroy')->name('teams.massDestroy');
    Route::post('teams/parse-csv-import', 'TeamController@parseCsvImport')->name('teams.parseCsvImport');
    Route::post('teams/process-csv-import', 'TeamController@processCsvImport')->name('teams.processCsvImport');
    Route::resource('teams', 'TeamController');

    // User Alerts
    Route::delete('user-alerts/destroy', 'UserAlertsController@massDestroy')->name('user-alerts.massDestroy');
    Route::get('user-alerts/read', 'UserAlertsController@read');
    Route::resource('user-alerts', 'UserAlertsController', ['except' => ['edit', 'update']]);

    // Expense Category
    Route::delete('expense-categories/destroy', 'ExpenseCategoryController@massDestroy')->name('expense-categories.massDestroy');
    Route::resource('expense-categories', 'ExpenseCategoryController');

    // Income Category
    Route::delete('income-categories/destroy', 'IncomeCategoryController@massDestroy')->name('income-categories.massDestroy');
    Route::resource('income-categories', 'IncomeCategoryController');

    // Expense
    Route::delete('expenses/destroy', 'ExpenseController@massDestroy')->name('expenses.massDestroy');
    Route::resource('expenses', 'ExpenseController');

    // Income
    Route::delete('incomes/destroy', 'IncomeController@massDestroy')->name('incomes.massDestroy');
    Route::resource('incomes', 'IncomeController');

    // Expense Report
    Route::delete('expense-reports/destroy', 'ExpenseReportController@massDestroy')->name('expense-reports.massDestroy');
    Route::resource('expense-reports', 'ExpenseReportController');

    // Currency
    Route::delete('currencies/destroy', 'CurrencyController@massDestroy')->name('currencies.massDestroy');
    Route::resource('currencies', 'CurrencyController');

    // Transaction Type
    Route::delete('transaction-types/destroy', 'TransactionTypeController@massDestroy')->name('transaction-types.massDestroy');
    Route::resource('transaction-types', 'TransactionTypeController');

    // Income Source
    Route::delete('income-sources/destroy', 'IncomeSourceController@massDestroy')->name('income-sources.massDestroy');
    Route::resource('income-sources', 'IncomeSourceController');

    // Client Status
    Route::delete('client-statuses/destroy', 'ClientStatusController@massDestroy')->name('client-statuses.massDestroy');
    Route::resource('client-statuses', 'ClientStatusController');

    // Project Status
    Route::delete('project-statuses/destroy', 'ProjectStatusController@massDestroy')->name('project-statuses.massDestroy');
    Route::resource('project-statuses', 'ProjectStatusController');

    // Client
    Route::delete('clients/destroy', 'ClientController@massDestroy')->name('clients.massDestroy');
    Route::resource('clients', 'ClientController');

    // Project
    Route::delete('projects/destroy', 'ProjectController@massDestroy')->name('projects.massDestroy');
    Route::resource('projects', 'ProjectController');

    // Note
    Route::delete('notes/destroy', 'NoteController@massDestroy')->name('notes.massDestroy');
    Route::resource('notes', 'NoteController');

    // Document
    Route::delete('documents/destroy', 'DocumentController@massDestroy')->name('documents.massDestroy');
    Route::post('documents/media', 'DocumentController@storeMedia')->name('documents.storeMedia');
    Route::post('documents/ckmedia', 'DocumentController@storeCKEditorImages')->name('documents.storeCKEditorImages');
    Route::resource('documents', 'DocumentController');

    // Transaction
    Route::delete('transactions/destroy', 'TransactionController@massDestroy')->name('transactions.massDestroy');
    Route::resource('transactions', 'TransactionController');

    // Client Report
    Route::delete('client-reports/destroy', 'ClientReportController@massDestroy')->name('client-reports.massDestroy');
    Route::resource('client-reports', 'ClientReportController');

    // Asset Category
    Route::delete('asset-categories/destroy', 'AssetCategoryController@massDestroy')->name('asset-categories.massDestroy');
    Route::resource('asset-categories', 'AssetCategoryController');

    // Asset Location
    Route::delete('asset-locations/destroy', 'AssetLocationController@massDestroy')->name('asset-locations.massDestroy');
    Route::resource('asset-locations', 'AssetLocationController');

    // Asset Status
    Route::delete('asset-statuses/destroy', 'AssetStatusController@massDestroy')->name('asset-statuses.massDestroy');
    Route::resource('asset-statuses', 'AssetStatusController');

    // Asset
    Route::delete('assets/destroy', 'AssetController@massDestroy')->name('assets.massDestroy');
    Route::post('assets/media', 'AssetController@storeMedia')->name('assets.storeMedia');
    Route::post('assets/ckmedia', 'AssetController@storeCKEditorImages')->name('assets.storeCKEditorImages');
    Route::resource('assets', 'AssetController');

    // Assets History
    Route::resource('assets-histories', 'AssetsHistoryController', ['except' => ['create', 'store', 'edit', 'update', 'show', 'destroy']]);

    // Vts
    Route::delete('vts/destroy', 'VtsController@massDestroy')->name('vts.massDestroy');
    Route::post('vts/parse-csv-import', 'VtsController@parseCsvImport')->name('vts.parseCsvImport');
    Route::post('vts/process-csv-import', 'VtsController@processCsvImport')->name('vts.processCsvImport');
    Route::resource('vts', 'VtsController');

    // Imei Models
    Route::delete('imei-models/destroy', 'ImeiModelsController@massDestroy')->name('imei-models.massDestroy');
    Route::post('imei-models/parse-csv-import', 'ImeiModelsController@parseCsvImport')->name('imei-models.parseCsvImport');
    Route::post('imei-models/process-csv-import', 'ImeiModelsController@processCsvImport')->name('imei-models.processCsvImport');
    Route::resource('imei-models', 'ImeiModelsController');

    // Imei Masters
    Route::delete('imei-masters/destroy', 'ImeiMastersController@massDestroy')->name('imei-masters.massDestroy');
    Route::post('imei-masters/parse-csv-import', 'ImeiMastersController@parseCsvImport')->name('imei-masters.parseCsvImport');
    Route::post('imei-masters/process-csv-import', 'ImeiMastersController@processCsvImport')->name('imei-masters.processCsvImport');
    Route::resource('imei-masters', 'ImeiMastersController');

    // Product Model
    Route::delete('product-models/destroy', 'ProductModelController@massDestroy')->name('product-models.massDestroy');
    Route::post('product-models/parse-csv-import', 'ProductModelController@parseCsvImport')->name('product-models.parseCsvImport');
    Route::post('product-models/process-csv-import', 'ProductModelController@processCsvImport')->name('product-models.processCsvImport');
    Route::resource('product-models', 'ProductModelController');

    // Product Masters
    Route::delete('product-masters/destroy', 'ProductMastersController@massDestroy')->name('product-masters.massDestroy');
    Route::post('product-masters/parse-csv-import', 'ProductMastersController@parseCsvImport')->name('product-masters.parseCsvImport');
    Route::post('product-masters/process-csv-import', 'ProductMastersController@processCsvImport')->name('product-masters.processCsvImport');
    Route::resource('product-masters', 'ProductMastersController');

    // Unbind Product
    Route::delete('unbind-products/destroy', 'UnbindProductController@massDestroy')->name('unbind-products.massDestroy');
    Route::resource('unbind-products', 'UnbindProductController');
  
    Route::post('unbind-products/details', [UnbindProductController::class, 'getProductDetails'])->name('unbind.products.details');
    Route::post('unbind-products/unbind', [UnbindProductController::class, 'unbind'])->name('unbind.products.unbind');

    // Current Stock
    Route::delete('current-stocks/destroy', 'CurrentStockController@massDestroy')->name('current-stocks.massDestroy');
    Route::post('current-stocks/parse-csv-import', 'CurrentStockController@parseCsvImport')->name('current-stocks.parseCsvImport');
    Route::post('current-stocks/process-csv-import', 'CurrentStockController@processCsvImport')->name('current-stocks.processCsvImport');
    Route::resource('current-stocks', 'CurrentStockController');

    // Stock Transfer
    Route::delete('stock-transfers/destroy', 'StockTransferController@massDestroy')->name('stock-transfers.massDestroy');
    Route::post('stock-transfers/parse-csv-import', 'StockTransferController@parseCsvImport')->name('stock-transfers.parseCsvImport');
    Route::post('stock-transfers/process-csv-import', 'StockTransferController@processCsvImport')->name('stock-transfers.processCsvImport');
    Route::resource('stock-transfers', 'StockTransferController');

    // Check Party Stock
    Route::delete('check-party-stocks/destroy', 'CheckPartyStockController@massDestroy')->name('check-party-stocks.massDestroy');
    Route::post('check-party-stocks/parse-csv-import', 'CheckPartyStockController@parseCsvImport')->name('check-party-stocks.parseCsvImport');
    Route::post('check-party-stocks/process-csv-import', 'CheckPartyStockController@processCsvImport')->name('check-party-stocks.processCsvImport');
    Route::resource('check-party-stocks', 'CheckPartyStockController');
    Route::get('/check-party-stocks/users/by-role', [CheckPartyStockController::class, 'getUsersByRole'])->name('users.byRole');
    Route::get('/check-party-stocks/get-users-by-role', [CheckPartyStockController::class, 'getUsersByRole'])->name('users.byRole');
   

    // Check Complain
    Route::delete('check-complains/destroy', 'CheckComplainController@massDestroy')->name('check-complains.massDestroy');
    Route::post('check-complains/media', 'CheckComplainController@storeMedia')->name('check-complains.storeMedia');
    Route::post('check-complains/ckmedia', 'CheckComplainController@storeCKEditorImages')->name('check-complains.storeCKEditorImages');
    Route::post('check-complains/parse-csv-import', 'CheckComplainController@parseCsvImport')->name('check-complains.parseCsvImport');
    Route::post('check-complains/process-csv-import', 'CheckComplainController@processCsvImport')->name('check-complains.processCsvImport');
    Route::resource('check-complains', 'CheckComplainController');

    // State
    Route::delete('states/destroy', 'StateController@massDestroy')->name('states.massDestroy');
    Route::post('states/parse-csv-import', 'StateController@parseCsvImport')->name('states.parseCsvImport');
    Route::post('states/process-csv-import', 'StateController@processCsvImport')->name('states.processCsvImport');
    Route::resource('states', 'StateController');

    // Districts
    Route::delete('districts/destroy', 'DistrictsController@massDestroy')->name('districts.massDestroy');
    Route::post('districts/parse-csv-import', 'DistrictsController@parseCsvImport')->name('districts.parseCsvImport');
    Route::post('districts/process-csv-import', 'DistrictsController@processCsvImport')->name('districts.processCsvImport');
    Route::resource('districts', 'DistrictsController');

    // Vehicle Type
    Route::delete('vehicle-types/destroy', 'VehicleTypeController@massDestroy')->name('vehicle-types.massDestroy');
    Route::post('vehicle-types/media', 'VehicleTypeController@storeMedia')->name('vehicle-types.storeMedia');
    Route::post('vehicle-types/ckmedia', 'VehicleTypeController@storeCKEditorImages')->name('vehicle-types.storeCKEditorImages');
    Route::post('vehicle-types/parse-csv-import', 'VehicleTypeController@parseCsvImport')->name('vehicle-types.parseCsvImport');
    Route::post('vehicle-types/process-csv-import', 'VehicleTypeController@processCsvImport')->name('vehicle-types.processCsvImport');
    Route::resource('vehicle-types', 'VehicleTypeController');

    // App Link
    Route::delete('app-links/destroy', 'AppLinkController@massDestroy')->name('app-links.massDestroy');
    Route::post('app-links/parse-csv-import', 'AppLinkController@parseCsvImport')->name('app-links.parseCsvImport');
    Route::post('app-links/process-csv-import', 'AppLinkController@processCsvImport')->name('app-links.processCsvImport');
    Route::resource('app-links', 'AppLinkController');

        // App Download
    Route::delete('app-downloads/destroy', 'AppDownloadController@massDestroy')->name('app-downloads.massDestroy');
    Route::post('app-downloads/media', 'AppDownloadController@storeMedia')->name('app-downloads.storeMedia');
    Route::post('app-downloads/ckmedia', 'AppDownloadController@storeCKEditorImages')->name('app-downloads.storeCKEditorImages');
    Route::post('app-downloads/parse-csv-import', 'AppDownloadController@parseCsvImport')->name('app-downloads.parseCsvImport');
    Route::post('app-downloads/process-csv-import', 'AppDownloadController@processCsvImport')->name('app-downloads.processCsvImport');
    Route::resource('app-downloads', 'AppDownloadController');


    // Activation Request
    Route::delete('activation-requests/destroy', 'ActivationRequestController@massDestroy')->name('activation-requests.massDestroy');
    Route::post('activation-requests/media', 'ActivationRequestController@storeMedia')->name('activation-requests.storeMedia');
    Route::post('activation-requests/ckmedia', 'ActivationRequestController@storeCKEditorImages')->name('activation-requests.storeCKEditorImages');
    Route::post('activation-requests/parse-csv-import', 'ActivationRequestController@parseCsvImport')->name('activation-requests.parseCsvImport');
    Route::post('activation-requests/process-csv-import', 'ActivationRequestController@processCsvImport')->name('activation-requests.processCsvImport');
    Route::resource('activation-requests', 'ActivationRequestController');

    // Attach Veichle
    Route::delete('attach-veichles/destroy', 'AttachVeichleController@massDestroy')->name('attach-veichles.massDestroy');
    Route::post('attach-veichles/parse-csv-import', 'AttachVeichleController@parseCsvImport')->name('attach-veichles.parseCsvImport');
    Route::post('attach-veichles/process-csv-import', 'AttachVeichleController@processCsvImport')->name('attach-veichles.processCsvImport');
    Route::resource('attach-veichles', 'AttachVeichleController');

    // Recharge Plan
    Route::delete('recharge-plans/destroy', 'RechargePlanController@massDestroy')->name('recharge-plans.massDestroy');
    Route::post('recharge-plans/parse-csv-import', 'RechargePlanController@parseCsvImport')->name('recharge-plans.parseCsvImport');
    Route::post('recharge-plans/process-csv-import', 'RechargePlanController@processCsvImport')->name('recharge-plans.processCsvImport');
    Route::resource('recharge-plans', 'RechargePlanController');

    // Recharge Request
    Route::delete('recharge-requests/destroy', 'RechargeRequestController@massDestroy')->name('recharge-requests.massDestroy');
    Route::post('recharge-requests/media', 'RechargeRequestController@storeMedia')->name('recharge-requests.storeMedia');
    Route::post('recharge-requests/ckmedia', 'RechargeRequestController@storeCKEditorImages')->name('recharge-requests.storeCKEditorImages');
    Route::post('recharge-requests/parse-csv-import', 'RechargeRequestController@parseCsvImport')->name('recharge-requests.parseCsvImport');
    Route::post('recharge-requests/process-csv-import', 'RechargeRequestController@processCsvImport')->name('recharge-requests.processCsvImport');
    Route::resource('recharge-requests', 'RechargeRequestController');

    Route::get('messenger', 'MessengerController@index')->name('messenger.index');
    Route::get('messenger/create', 'MessengerController@createTopic')->name('messenger.createTopic');
    Route::post('messenger', 'MessengerController@storeTopic')->name('messenger.storeTopic');
    Route::get('messenger/inbox', 'MessengerController@showInbox')->name('messenger.showInbox');
    Route::get('messenger/outbox', 'MessengerController@showOutbox')->name('messenger.showOutbox');
    Route::get('messenger/{topic}', 'MessengerController@showMessages')->name('messenger.showMessages');
    Route::delete('messenger/{topic}', 'MessengerController@destroyTopic')->name('messenger.destroyTopic');
    Route::post('messenger/{topic}/reply', 'MessengerController@replyToTopic')->name('messenger.reply');
    Route::get('messenger/{topic}/reply', 'MessengerController@showReply')->name('messenger.showReply');
    Route::get('team-members', 'TeamMembersController@index')->name('team-members.index');
    Route::post('team-members', 'TeamMembersController@invite')->name('team-members.invite');
    Route::get('/admin/product-models/details/{id}', [App\Http\Controllers\Admin\ProductMastersController::class, 'getModelDetails'])->name('product-models.details');
// Add Customer Vehicle
    Route::delete('add-customer-vehicles/destroy', 'AddCustomerVehicleController@massDestroy')->name('add-customer-vehicles.massDestroy');
    Route::post('add-customer-vehicles/media', 'AddCustomerVehicleController@storeMedia')->name('add-customer-vehicles.storeMedia');
    Route::post('add-customer-vehicles/ckmedia', 'AddCustomerVehicleController@storeCKEditorImages')->name('add-customer-vehicles.storeCKEditorImages');
    Route::post('add-customer-vehicles/parse-csv-import', 'AddCustomerVehicleController@parseCsvImport')->name('add-customer-vehicles.parseCsvImport');
    Route::post('add-customer-vehicles/process-csv-import', 'AddCustomerVehicleController@processCsvImport')->name('add-customer-vehicles.processCsvImport');
    Route::resource('add-customer-vehicles', 'AddCustomerVehicleController');
    Route::post('add-customer-vehicles/validate-password', [App\Http\Controllers\Admin\AddCustomerVehicleController::class, 'validatePassword'])->name('validate-password');

    // Complain Category
    Route::delete('complain-categories/destroy', 'ComplainCategoryController@massDestroy')->name('complain-categories.massDestroy');
    Route::post('complain-categories/media', 'ComplainCategoryController@storeMedia')->name('complain-categories.storeMedia');
    Route::post('complain-categories/ckmedia', 'ComplainCategoryController@storeCKEditorImages')->name('complain-categories.storeCKEditorImages');
    Route::post('complain-categories/parse-csv-import', 'ComplainCategoryController@parseCsvImport')->name('complain-categories.parseCsvImport');
    Route::post('complain-categories/process-csv-import', 'ComplainCategoryController@processCsvImport')->name('complain-categories.processCsvImport');
    Route::resource('complain-categories', 'ComplainCategoryController');
    Route::get('check-complains/{id}/pdf', [CheckComplainController::class, 'showPdf'])->name('check-complains.pdf');



    Route::get('get-users-by-role', 'StockTransferController@getUsersByRole')->name('get.users.by.role');
    Route::get('admin/get-product-details', [StockTransferController::class, 'getProductDetails'])->name('get.product.details');

    Route::get('/admin/get-imei-number', [StockTransferController::class, 'getImeiNumber'])->name('get.imei.number');
    Route::get('/get-users-by-roles', [ActivationRequestController::class, 'getUsersByRole'])->name('get.users.by.roles');

    Route::get('get-party-products', [ActivationRequestController::class, 'getPartyProducts'])->name('getPartyProducts');
    Route::get('/get-user-details/{id}', [ActivationRequestController::class, 'getUserDetails'])->name('get.user.details');
    Route::get('users/search', [ActivationRequestController::class, 'search'])->name('users.search');


    Route::get('get-child-customers', [ActivationRequestController::class, 'getChildCustomers'])->name('getChildCustomers');

    Route::get('admin/activation-requests/{id}/activate', [App\Http\Controllers\Admin\ActivationRequestController::class, 'confirmActivate'])->name('activation-requests.activate');
    Route::post('/admin/activation-requests/{id}/command', [ActivationRequestController::class, 'processCommand'])->name('activation-requests.command');


    Route::get('stock-history', [CurrentStockController::class, 'stockHistory'])
    ->name('reports.stock-history')
    ->middleware('can:stock_history_access');

    Route::post('activation-requests/store-media', [ActivationRequestController::class, 'storeMedia'])
    ->name('activation-requests.storeMedia');

    Route::get('stock-history/{id}', [CurrentStockController::class, 'showHistory'])->name('reports.stock-history.show');

    Route::middleware(['auth'])->group(function () {
        Route::get('/vehicle-photos/{vehicle}', [RechargeRequestController::class, 'getVehiclePhotos'])
            ->name('vehicle.photos');
    });
    Route::get('recharge-plan-details/{id}', [App\Http\Controllers\Admin\RechargePlanController::class, 'getPlanDetails']);
    Route::get('/get-customer-vehicles/{userId}', [App\Http\Controllers\Admin\RechargeRequestController::class, 'getCustomerVehicles']);
    // vehicle sharing
    Route::post('vehicle-sharing/store', [VehicleSharingController::class, 'store'])->name('vehicle-sharing.store');
    Route::get('users/create/search', [App\Http\Controllers\Admin\UsersController::class, 'search'])->name('users.search');
    Route::post('/vehicle-sharing/remove', [VehicleSharingController::class, 'remove'])
    ->name('vehicle-sharing.remove');
    Route::resource('kyc-recharges', KycRechargeController::class);
// readom history route
    Route::get('/commission/total', [App\Http\Controllers\Admin\CommissionController::class, 'totalCommission'])->name('commission.total');
    Route::get('/commission/history', [App\Http\Controllers\Admin\CommissionController::class, 'history'])->name('commission.history');

    Route::resource('delete-data', DeleteDataController::class);
     // ✅ CSV Import route
    Route::post('delete-data/parse-csv-import', [DeleteDataController::class, 'parseCsvImport'])
        ->name('delete-data.parseCsvImport');
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});


Route::get('/admin/reports/download/{id}', [CurrentStockController::class, 'download'])->name('admin.reports.download');
Route::get('/admin/reports/print/{id}', [CurrentStockController::class, 'print'])->name('admin.reports.print');
Route::get('/alerts-fetch', [App\Http\Controllers\Admin\UserAlertsController::class, 'fetch'])->name('admin.alerts.fetch');

// pdf route 
Route::get('recharge-requests/{rechargeRequest}/download-pdf', [App\Http\Controllers\Admin\RechargeRequestController::class, 'downloadPdf'])->name('admin.rechargerequests.downloadPdf');
Route::get('admin/activationrequests/invoice/{id}', [App\Http\Controllers\Admin\ActivationRequestController::class, 'downloadInvoice'])->name('admin.activationrequests.invoice');
  Route::get('activationrequests/{id}/invoice', [App\Http\Controllers\Admin\CheckComplainController::class, 'invoice'])->name('admin.checkcomplains.invoice');
  Route::get('admin/current-stocks/{id}/invoice', [CurrentStockController::class, 'invoice'])->name('admin.current-stocks.invoice');
Route::get('/report/product-pie-chart', [App\Http\Controllers\HomeController::class, 'productPieChart'])->name('report.product.pie');

Route::post('admin/kyc-recharges', [App\Http\Controllers\Admin\KycRechargeController::class, 'store'])->name('admin.kyc-recharges.store');
Route::post('admin/kyc-recharges/{id}', [App\Http\Controllers\Admin\KycRechargeController::class, 'edit'])->name('admin.kyc-recharge.update');

Route::post('/admin/kyc-recharges/{id}/payment-callback-json', [App\Http\Controllers\Admin\KycRechargeController::class, 'paymentCallbackJson'])->name('admin.kyc-recharges.payment-callback-json');



