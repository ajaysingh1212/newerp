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
    
            $data = $vehicles->map(function ($vehicle) {
                $requestDate = $vehicle->request_date 
                    ? Carbon::createFromFormat('d-m-Y', $vehicle->request_date) 
                    : null;
    
                $productModel = $vehicle->product_master?->product_model;
    
                // Check recharge exist karta hai ya nahi
                $hasRecharge = RechargeRequest::where('vehicle_number', $vehicle->vehicle_number)->exists();
    
                if ($hasRecharge) {
                    // Recharge hua hai → vehicle table ki dates direct use karo
                    $warrantyExpiry     = $vehicle->warranty;
                    $subscriptionExpiry = $vehicle->subscription;
                    $amcExpiry          = $vehicle->amc;
                } else {
                    // Recharge nahi hua hai → calculate karo activation date se
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
    
                return [
                    'vehicle_number'  => $vehicle->vehicle_number,
                    'product_model'   => $productModel?->product_model,
                    'status'          => $vehicle->status,
                    'activation_date' => $requestDate ? $requestDate->format('Y-m-d') : null,
                    'warranty'        => $warrantyExpiry,
                    'subscription'    => $subscriptionExpiry,
                    'amc'             => $amcExpiry,
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

}
