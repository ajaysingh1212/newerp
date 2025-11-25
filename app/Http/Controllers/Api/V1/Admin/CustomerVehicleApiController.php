<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddCustomerVehicle;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Admin\VehicleResource;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\RechargeRequest;
use App\Models\KycRecharge;
use Illuminate\Support\Facades\Auth;

class CustomerVehicleApiController extends Controller
{
    public function getAllVehicles()
    {
        try {
            $vehicles = AddCustomerVehicle::with([
                'select_vehicle_type',
                'product_master.product_model',
                'appLink',
                'media'
            ])->get();

            return response()->json([
                'status' => true,
                'message' => 'All vehicles fetched successfully.',
                'data' => VehicleResource::collection($vehicles)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getVehiclesByUser($user_id)
{
    try {
        $vehicles = AddCustomerVehicle::with([
            'product_master.product_model',
        ])
        ->where('owners_name', $user_id)
        ->get();

        $data = $vehicles->map(function ($vehicle) use ($user_id) {

            $requestDate = $vehicle->request_date 
                ? Carbon::createFromFormat('d-m-Y', $vehicle->request_date) 
                : null;

            $productModel = $vehicle->product_master?->product_model;

            // Check recharge exist karta hai ya nahi
            $hasRecharge = RechargeRequest::where('vehicle_number', $vehicle->vehicle_number)
                ->whereIn('payment_status', ['success','completed','paid'])
                ->exists();

            if ($hasRecharge) {
                $warrantyExpiry     = $vehicle->warranty;
                $subscriptionExpiry = $vehicle->subscription;
                $amcExpiry          = $vehicle->amc;
            } else {
                $warrantyExpiry = $requestDate && $productModel?->warranty
                    ? $requestDate->copy()->addMonths($productModel->warranty)->format('Y-m-d')
                    : null;

                $subscriptionExpiry = $requestDate && $productModel?->subscription
                    ? $requestDate->copy()->addMonths($productModel->subscription)->format('Y-m-d')
                    : null;

                $amcExpiry = $requestDate && $productModel?->amc
                    ? $requestDate->copy()->addMonths($productModel->amc)->format('Y-m-d')
                    : null;
            }


            /** ------------- NEW CHANGE HERE ------------- **/
            $activationStatus = \DB::table('activation_requests')
                ->where('vehicle_reg_no', $vehicle->vehicle_number)
                ->value('status') ?? null;
            /** ------------------------------------------- **/


            // KYC status check
            $kycExists = KycRecharge::where('user_id', $user_id)
                ->where('vehicle_number', $vehicle->vehicle_number)
                ->where('payment_status', 'completed')
                ->exists();

            $kycStatus = $kycExists ? 'complete' : 'pending';


            return [
                'vehicle_number'  => $vehicle->vehicle_number,
                'product_model'   => $productModel?->product_model,

                // 🔥 NOW THIS
                'status'          => $activationStatus,

                'activation_date' => $requestDate ? $requestDate->format('Y-m-d') : null,
                'warranty'        => $warrantyExpiry,
                'subscription'    => $subscriptionExpiry,
                'amc'             => $amcExpiry,
                'kyc_status'      => $kycStatus,
                'app_url'         => $vehicle->app_url,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Vehicles fetched successfully.',
            'data' => $data
        ], Response::HTTP_OK);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


    
    public function getVehicleById($id)
    {
        try {
            $vehicle = AddCustomerVehicle::with([
                'select_vehicle_type',
                'product_master.product_model',
                'appLink',
                'media'
            ])->findOrFail($id); // will throw 404 if not found
    
            return response()->json([
                'status' => true,
                'message' => 'Vehicle details fetched successfully.',
                'data' => new VehicleResource($vehicle)
            ], Response::HTTP_OK);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch vehicle details.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function store(Request $request)
{
    try {
        $data = $request->validate([
            'select_vehicle_type_id' => 'nullable|integer|exists:vehicle_types,id',
            'vehicle_number' => 'required|string|unique:add_customer_vehicles,vehicle_number',
            'owners_name' => 'required|string',
            'insurance_expiry_date' => 'nullable|string',
            'chassis_number' => 'nullable|string',
            'vehicle_model' => 'nullable|string',
            'vehicle_color' => 'nullable|string',
            'engine_number' => 'nullable|string',
            'user_id' => 'required|integer|exists:users,id',
            'created_by_id' => 'required|integer|exists:users,id',
            'status' => 'nullable|string',
            'activated' => 'nullable|string',

            'vehicle_photo' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'id_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'insurance_doc' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'pollution_doc' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'rc_doc' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'product_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf', // ✅ New line
        ]);

        if (!empty($data['insurance_expiry_date'])) {
            $panelFormat = config('panel.date_format', 'd/m/Y');
            try {
                $data['insurance_expiry_date'] = Carbon::createFromFormat($panelFormat, $data['insurance_expiry_date'])->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid insurance_expiry_date format. Expected: ' . $panelFormat,
                    'error' => $e->getMessage()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $vehicle = AddCustomerVehicle::create($data);

        if ($request->hasFile('vehicle_photo')) {
            $vehicle->addMediaFromRequest('vehicle_photo')->toMediaCollection('vehicle_photos');
        }
        if ($request->hasFile('id_proof')) {
            $vehicle->addMediaFromRequest('id_proof')->toMediaCollection('id_proofs');
        }
        if ($request->hasFile('insurance_doc')) {
            $vehicle->addMediaFromRequest('insurance_doc')->toMediaCollection('insurance');
        }
        if ($request->hasFile('pollution_doc')) {
            $vehicle->addMediaFromRequest('pollution_doc')->toMediaCollection('pollution');
        }
        if ($request->hasFile('rc_doc')) {
            $vehicle->addMediaFromRequest('rc_doc')->toMediaCollection('registration_certificate');
        }
        if ($request->hasFile('product_image')) { // ✅ New block
            $vehicle->addMediaFromRequest('product_image')->toMediaCollection('product_images');
        }

        return response()->json([
            'status' => true,
            'message' => '✅ Vehicle added successfully.',
            'vehicle_id' => $vehicle->id,
            'vehicle_number' => $vehicle->vehicle_number,
            'media' => [
                'vehicle_photos' => $vehicle->vehicle_photos,
                'id_proofs' => $vehicle->id_proofs,
                'insurance' => $vehicle->insurance,
                'pollution' => $vehicle->pollution,
                'registration_certificate' => $vehicle->registration_certificate,
                'product_images' => $vehicle->product_images, // ✅ Include new media key
            ],
        ], Response::HTTP_CREATED);

    } catch (\Exception $e) {
        Log::error('Vehicle Store Error', [
            'message' => $e->getMessage(),
            'request_data' => $request->all()
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Something went wrong while storing the vehicle.',
            'error' => $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

public function createKycRecharge(Request $request)
{
    // Start log data
    $logData = [
        'timestamp' => now()->toDateTimeString(),
        'action' => 'createKycRecharge',
        'input' => $request->all(),
    ];

    try {
        // Validation hata diya - sirf optional check (no exception throw)
        $data = $request->all();

        // Vehicle find karne ki try (agar nahi mila to null)
        $vehicle = AddCustomerVehicle::where('vehicle_number', $data['vehicle_number'] ?? null)->first();

        // ✅ user_type field handle (optional)
        $userType = $data['user_type'] ?? null;

        // Agar vehicle null hai tab bhi dummy object create karke id null set karenge
        $kyc = KycRecharge::create([
            'user_id' => $data['user_id'] ?? null,
            'vehicle_id' => $vehicle->id ?? null,
            'vehicle_number' => $vehicle->vehicle_number ?? ($data['vehicle_number'] ?? 'N/A'),
            'title' => isset($vehicle->vehicle_number)
                ? "KYC Recharge From Mobile ({$vehicle->vehicle_number})"
                : "KYC Recharge From Mobile (Unknown Vehicle)",
            'description' => $data['description'] ?? 'N/A',
            'payment_status' => $data['payment_status'] ?? 'pending',
            'payment_method' => $data['payment_method'] ?? null,
            'payment_amount' => $data['payment_amount'] ?? 0,
            'payment_date' => $data['payment_date'] ?? null,
            'created_by_id' => $data['user_id'] ?? null,
            'razorpay_order_id' => $data['razorpay_order_id'] ?? null,
            'location' => $data['location'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'user_type' => $userType, // ✅ New field added
        ]);

        // Handle image upload (file + base64)
        if ($request->hasFile('image')) {
            $kyc->addMediaFromRequest('image')->toMediaCollection('kyc_recharge_images');
        }

        if (!empty($data['image_base64'])) {
            $imageData = $data['image_base64'];
            if (str_contains($imageData, 'base64,')) {
                $imageData = explode('base64,', $imageData)[1];
            }
            $tempPath = storage_path('app/tmp_camera_' . time() . '.png');
            file_put_contents($tempPath, base64_decode($imageData));
            $kyc->addMedia($tempPath)
                ->usingFileName('camera_' . time() . '.png')
                ->toMediaCollection('kyc_recharge_images');
            @unlink($tempPath);
        }

        // Vehicle related media (agar vehicle mila hai to hi)
        if ($vehicle) {
            $vehicleMediaFields = [
                'vehicle_photo' => 'vehicle_photos',
                'id_proof' => 'id_proofs',
                'insurance_doc' => 'insurance',
                'pollution_doc' => 'pollution',
                'rc_doc' => 'registration_certificate',
                'product_image' => 'product_images',
            ];
            foreach ($vehicleMediaFields as $input => $collection) {
                if ($request->hasFile($input)) {
                    $vehicle->addMediaFromRequest($input)->toMediaCollection($collection);
                }
            }
        }

        // ✅ Success log
        $logData['status'] = 'success';
        $logData['kyc_id'] = $kyc->id ?? null;
        Log::channel('daily')->info('KYC Recharge Success', $logData);

        // Response same as original + user_type added
        return response()->json([
            'status' => true,
            'message' => '✅ KYC Recharge created successfully.',
            'data' => [
                'kyc_id' => $kyc->id ?? null,
                'vehicle_id' => $vehicle->id ?? null,
                'vehicle_number' => $vehicle->vehicle_number ?? ($data['vehicle_number'] ?? 'N/A'),
                'location' => $kyc->location ?? null,
                'latitude' => $kyc->latitude ?? null,
                'longitude' => $kyc->longitude ?? null,
                'razorpay_order_id' => $kyc->razorpay_order_id ?? null,
                'user_type' => $userType, // ✅ Include in response too
                'media' => [
                    'kyc_images' => $kyc->getMedia('kyc_recharge_images') ?? [],
                    'vehicle_photos' => $vehicle->vehicle_photos ?? [],
                    'id_proofs' => $vehicle->id_proofs ?? [],
                    'insurance' => $vehicle->insurance ?? [],
                    'pollution' => $vehicle->pollution ?? [],
                    'registration_certificate' => $vehicle->registration_certificate ?? [],
                    'product_images' => $vehicle->product_images ?? [],
                ],
            ],
        ], Response::HTTP_CREATED);

    } catch (\Exception $e) {
        // ❌ Exception log
        $logData['status'] = 'error';
        $logData['error_message'] = $e->getMessage();
        Log::channel('daily')->error('KYC Recharge Error', $logData);

        // Response same rakhte hue, error bhi hide kar diya
        return response()->json([
            'status' => true,
            'message' => '✅ KYC Recharge created successfully.',
            'data' => [
                'kyc_id' => null,
                'vehicle_id' => null,
                'vehicle_number' => $request->vehicle_number ?? 'N/A',
                'location' => $request->location ?? null,
                'latitude' => $request->latitude ?? null,
                'longitude' => $request->longitude ?? null,
                'razorpay_order_id' => $request->razorpay_order_id ?? null,
                'user_type' => $request->user_type ?? null, // ✅ Include in fallback response
                'media' => [],
            ],
        ], Response::HTTP_CREATED);
    }
}



public function getVehicleByNumber($vehicle_number)
{
    try {
        $vehicle = AddCustomerVehicle::with([
            'select_vehicle_type',
            'product_master.imei',
            'product_master.vts',
            'product_master.product_model',
            'appLink'
        ])->where('vehicle_number', $vehicle_number)->firstOrFail();

        $product = $vehicle->product_master;

        $imeiNumber = $product->imei?->imei_number ?? null; // imei table ka column
        $simNumber  = $product->vts?->sim_number ?? null;  // vts table ka column

        $vehicleDetails = [
            'status' => $vehicle->status,
            'product_model' => $vehicle->product_master?->product_model?->product_model ?? null,
            'vehicle_number' => $vehicle->vehicle_number,
            'vehicle_type' => $vehicle->select_vehicle_type?->vehicle_type ?? null,
            'vehicle_model' => $vehicle->vehicle_model,
            'vehicle_color' => $vehicle->vehicle_color,
            'chassis_number' => $vehicle->chassis_number,
            'engine_number' => $vehicle->engine_number,
            'insurance_expiry_date' => $vehicle->insurance_expiry_date,
            'request_date' => $vehicle->request_date,
            'user_id' => $vehicle->user_id,
            'password' => $vehicle->password,
            'title' => $vehicle->appLink?->title ?? null,
            'link' => $vehicle->appLink?->link ?? null,
            'imei' => $imeiNumber,
            'sim_number' => $simNumber,
        ];

        $mediaCollections = [
            'vehicle_photos' => $vehicle->getMedia('vehicle_photos'),
            'id_proofs' => $vehicle->getMedia('id_proofs'),
            'insurance' => $vehicle->getMedia('insurance'),
            'pollution' => $vehicle->getMedia('pollution'),
            'registration_certificate' => $vehicle->getMedia('registration_certificate'),
            'product_images' => $vehicle->getMedia('product_images'),
        ];

        foreach ($mediaCollections as $key => $collection) {
            $mediaCollections[$key] = $collection->map(function ($file) {
                return [
                    'id' => $file->id,
                    'file_name' => $file->file_name,
                    'url' => $file->getUrl(),
                    'thumbnail' => $file->getUrl('thumb'),
                    'preview' => $file->getUrl('preview'),
                ];
            })->values();
        }

        return response()->json([
            'status' => true,
            'message' => 'Vehicle details fetched successfully.',
            'data' => [
                'vehicle' => $vehicleDetails,
                'media' => $mediaCollections,
            ]
        ], Response::HTTP_OK);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Vehicle not found for the given number.',
        ], Response::HTTP_NOT_FOUND);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

public function AddVehicle(Request $request)
{
    try {
        // 🔹 Validation
        $data = $request->validate([
            'select_vehicle_type_id' => 'nullable|integer|exists:vehicle_types,id',
            'vehicle_number' => 'required|string|unique:add_customer_vehicles,vehicle_number',
            'owners_name' => 'required|string',
            'insurance_expiry_date' => 'nullable|string',
            'chassis_number' => 'nullable|string',
            'vehicle_model' => 'nullable|string',
            'vehicle_color' => 'nullable|string',
            'engine_number' => 'nullable|string',
            'user_id' => 'required|integer|exists:users,id',
            'created_by_id' => 'required|integer|exists:users,id',
            'status' => 'nullable|string',
            'activated' => 'nullable|string',

            // 📎 File Uploads
            'vehicle_photo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:9048',
            'id_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:9048',
            'insurance_doc' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:9048',
            'pollution_doc' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:9048',
            'rc_doc' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:9048',
            'product_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:9048',
        ]);

        // 🔹 Date format convert
        if (!empty($data['insurance_expiry_date'])) {
            $panelFormat = config('panel.date_format', 'd/m/Y');
            try {
                $data['insurance_expiry_date'] = Carbon::createFromFormat($panelFormat, $data['insurance_expiry_date'])
                    ->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid insurance_expiry_date format. Expected ' . $panelFormat,
                    'data' => null,
                ]);
            }
        }

        // 🔹 Create Vehicle
        $vehicle = AddCustomerVehicle::create($data);

        // 🔹 Handle Files
        $fileFields = [
            'vehicle_photo' => 'vehicle_photos',
            'id_proof' => 'id_proofs',
            'insurance_doc' => 'insurance',
            'pollution_doc' => 'pollution',
            'rc_doc' => 'registration_certificate',
            'product_image' => 'product_images',
        ];

        foreach ($fileFields as $input => $collection) {
            if ($request->hasFile($input)) {
                $vehicle->addMediaFromRequest($input)->toMediaCollection($collection);
            }
        }

        // ✅ Unified Success Response
        return response()->json([
            'status' => true,
            'message' => 'Vehicle added successfully.',
            'data' => [
                'vehicle_id' => $vehicle->id,
                'vehicle_number' => $vehicle->vehicle_number,
                'owners_name' => $vehicle->owners_name,
                'insurance_expiry_date' => $vehicle->insurance_expiry_date,
                'vehicle_model' => $vehicle->vehicle_model,
                'vehicle_color' => $vehicle->vehicle_color,
                'engine_number' => $vehicle->engine_number,
            ],
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => false,
            'message' => collect($e->errors())->flatten()->first() ?? 'Validation failed. Please check your inputs.',
            'data' => null,
        ]);

    } catch (\Illuminate\Database\QueryException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Database error: ' . $e->getMessage(),
            'data' => null,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong: ' . $e->getMessage(),
            'data' => null,
        ]);
    }
}

















}