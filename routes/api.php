<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // Permissions
    Route::apiResource('permissions', 'PermissionsApiController');

    // Roles
    Route::apiResource('roles', 'RolesApiController');

    // Users
    Route::post('users/media', 'UsersApiController@storeMedia')->name('users.storeMedia');
    Route::apiResource('users', 'UsersApiController');

    // Team
    Route::apiResource('teams', 'TeamApiController');

    // Assets History
    Route::apiResource('assets-histories', 'AssetsHistoryApiController', ['except' => ['store', 'show', 'update', 'destroy']]);

    // Vts
    Route::apiResource('vts', 'VtsApiController');

    // Imei Models
    Route::apiResource('imei-models', 'ImeiModelsApiController');

    // Imei Masters
    Route::apiResource('imei-masters', 'ImeiMastersApiController');

    // Product Model
    Route::apiResource('product-models', 'ProductModelApiController');

    // Product Masters
    Route::apiResource('product-masters', 'ProductMastersApiController');

    // Current Stock
    Route::apiResource('current-stocks', 'CurrentStockApiController');

    // Stock Transfer
    Route::apiResource('stock-transfers', 'StockTransferApiController');

    // Check Party Stock
    Route::apiResource('check-party-stocks', 'CheckPartyStockApiController');

    // Check Complain
    Route::post('check-complains/media', 'CheckComplainApiController@storeMedia')->name('check-complains.storeMedia');
    Route::apiResource('check-complains', 'CheckComplainApiController');

    // State
    Route::apiResource('states', 'StateApiController');

    // Districts
    Route::apiResource('districts', 'DistrictsApiController');

    // Vehicle Type
    Route::post('vehicle-types/media', 'VehicleTypeApiController@storeMedia')->name('vehicle-types.storeMedia');
    Route::apiResource('vehicle-types', 'VehicleTypeApiController');

    // App Link
    Route::apiResource('app-links', 'AppLinkApiController');

    // Activation Request
    Route::post('activation-requests/media', 'ActivationRequestApiController@storeMedia')->name('activation-requests.storeMedia');
    Route::apiResource('activation-requests', 'ActivationRequestApiController');

    // Attach Veichle
    Route::apiResource('attach-veichles', 'AttachVeichleApiController');

    // Recharge Plan
    Route::apiResource('recharge-plans', 'RechargePlanApiController');

    // Recharge Request
    Route::post('recharge-requests/media', 'RechargeRequestApiController@storeMedia')->name('recharge-requests.storeMedia');
    Route::apiResource('recharge-requests', 'RechargeRequestApiController');
});



Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin'], function () {

    // 🔐 User Login
    Route::post('login', 'UsersApiController@login')->name('login');
     
    // 📄 Get User Details by ID
    Route::get('user-details/{id}', 'UsersApiController@getUserById')->name('user.details');
    
    // 🧾 User Register API
    Route::post('register', 'UsersApiController@register')->name('register');
    
    // 🚘 Get All Vehicle Types
    Route::get('vehicle-types', 'VehicleTypeApiController@getAllVehicleTypes')->name('vehicle-types');
    
    // 📩 Submit Complain
    Route::post('create-complain', 'CheckComplainApiController@storeComplain')->name('create-complain');
    
    // 📝 Get Complaints by User ID
    Route::get('complaints-by-user/{user_id}', 'CheckComplainApiController@getComplaintsByUser')->name('complaints.by-user');
    
    // 📋 Get All Complain Categories
    Route::get('complain-categories', 'ComplainCategoryApiController@index')->name('complain-categories.index');
    
    // 🚚 Get vehicles-by-user
    Route::get('vehicles-by-user/{user_id}', 'CustomerVehicleApiController@getVehiclesByUser')->name('vehicles.by-user');
    
    // 🚙 Get Single Vehicle by Vehicle ID
    Route::get('vehicle-by-id/{id}', 'CustomerVehicleApiController@getVehicleById')->name('vehicle.by-id');
    
    // 🚚 Get All Vehicles in System
    Route::get('all-vehicles', 'CustomerVehicleApiController@getAllVehicles')->name('vehicles.all');
    
    // ➕ Add New Customer Vehicle
    Route::post('add-customer-vehicle', 'CustomerVehicleApiController@store')->name('add-customer-vehicle');
    
    // 💳 Get All Recharge Plans
    Route::get('all-recharge-plans', 'RechargePlanApiController@getAllPlans')->name('recharge-plans.all');
    
    // 🔌 Submit Recharge Request
    Route::post('submit-recharge', 'RechargeRequestApiController@submitRecharge')->name('submit-recharge');

    
    // 📜 Get Recharge History by User ID
    Route::get('recharge-history/{user_id}', 'RechargeRequestApiController@getRechargeHistoryByUser')->name('recharge-history.by-user');
    
    // 📋 Get Activation Requests by User ID
    Route::get('activation-requests-by-user/{user_id}', 'ActivationRequestApiController@getActivationRequestsByUser')->name('activation-requests.by-user');
    
    Route::post('submit-alert', 'UserAlertApiController@submit')->name('submit-alert');

    Route::get('alerts/{user_id}', 'UserAlertApiController@fetchByUserId')->name('alerts.by-user');
    
    // ➕ Upload Profile Photo (no auth)
   Route::post('user/{user_id}/upload-profile-photo', 'UsersApiController@uploadProfilePhoto')->name('user.upload-profile-photo');

   Route::post('create-kyc-recharge', 'CustomerVehicleApiController@createKycRecharge')->name('kyc-recharge.create');

   // 🚗 Get Vehicle by Vehicle Number
    Route::get('vehicle-by-number/{vehicle_number}', 'CustomerVehicleApiController@getVehicleByNumber')->name('vehicle.by-number');


    // ➕ Password Reset Request
    Route::post('password-reset', 'UsersApiController@sendPasswordResetLink')->name('password.reset');

    
   
    
    
});


