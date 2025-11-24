<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreRechargeRequestRequest;
use App\Http\Requests\UpdateRechargeRequestRequest;
use App\Http\Resources\Admin\RechargeRequestResource;
use App\Models\RechargeRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Carbon\Carbon;
use App\Models\AddCustomerVehicle;
use App\Models\ProductMaster;

use App\Models\RechargePlan;

class RechargeRequestApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('recharge_request_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new RechargeRequestResource(RechargeRequest::with(['user', 'product', 'select_recharge', 'team'])->get());
    }

    public function store(StoreRechargeRequestRequest $request)
    {
        $rechargeRequest = RechargeRequest::create($request->all());

        if ($request->input('attechment', false)) {
            $rechargeRequest->addMedia(storage_path('tmp/uploads/' . basename($request->input('attechment'))))->toMediaCollection('attechment');
        }

        return (new RechargeRequestResource($rechargeRequest))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(RechargeRequest $rechargeRequest)
    {
        abort_if(Gate::denies('recharge_request_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new RechargeRequestResource($rechargeRequest->load(['user', 'product', 'select_recharge', 'team']));
    }

    public function update(UpdateRechargeRequestRequest $request, RechargeRequest $rechargeRequest)
    {
        $rechargeRequest->update($request->all());

        if ($request->input('attechment', false)) {
            if (! $rechargeRequest->attechment || $request->input('attechment') !== $rechargeRequest->attechment->file_name) {
                if ($rechargeRequest->attechment) {
                    $rechargeRequest->attechment->delete();
                }
                $rechargeRequest->addMedia(storage_path('tmp/uploads/' . basename($request->input('attechment'))))->toMediaCollection('attechment');
            }
        } elseif ($rechargeRequest->attechment) {
            $rechargeRequest->attechment->delete();
        }

        return (new RechargeRequestResource($rechargeRequest))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(RechargeRequest $rechargeRequest)
    {
        abort_if(Gate::denies('recharge_request_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rechargeRequest->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
    
    public function submitRecharge(Request $request)
    {
        try {
            // Validation
            $request->validate([
                'user_id'            => 'required|integer',
                'vehicle_number'     => 'required|string',
                'select_recharge_id' => 'required|integer',
                'notes'              => 'nullable|string',
                'payment_method'     => 'required|string',
                'payment_status'     => 'required|string',
                'payment_amount'     => 'required|numeric',
                'razorpay_payment_id'=> 'nullable|string',
                'redeem_amount'      => 'nullable|numeric',
                'payment_id'         => 'required|string',
                'created_by_id'      => 'required|integer',
                'warranty'           => 'nullable|string',
                'subscription'       => 'nullable|string',
                'amc'                => 'nullable|string',
            ]);
    
            // Recharge request create
            $recharge = RechargeRequest::create([
                'user_id'             => $request->user_id,
                'vehicle_number'      => $request->vehicle_number,
                'select_recharge_id'  => $request->select_recharge_id,
                'notes'               => $request->notes,
                'payment_method'      => $request->payment_method,
                'payment_status'      => $request->payment_status,
                'payment_amount'      => $request->payment_amount,
                'payment_date'        => now(), // auto current date
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'redeem_amount'       => $request->redeem_amount ?? 0,
                'payment_id'          => $request->payment_id,
                'created_by_id'       => $request->created_by_id,
            ]);
    
            // Vehicle fetch
            $vehicle = AddCustomerVehicle::where('vehicle_number', $request->vehicle_number)->first();
            if (!$vehicle) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vehicle not found.',
                ], Response::HTTP_NOT_FOUND);
            }
    
            // Recharge plan fetch
            $plan = RechargePlan::find($request->select_recharge_id);
            if (!$plan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Recharge plan not found.',
                ], Response::HTTP_NOT_FOUND);
            }
    
            // Expiry calculation
            $today = Carbon::now();
            $updatedFields = [];
            $responseVehicle = ['vehicle_number' => $vehicle->vehicle_number];
    
            // Warranty
            if (!is_null($plan->warranty_duration)) {
                if ($request->warranty === "Expired") {
                    $expiry = $today->copy()->addMonths($plan->warranty_duration);
                } else {
                    $expiry = Carbon::parse($request->warranty)->addMonths($plan->warranty_duration);
                }
                $updatedFields['warranty'] = $expiry;
                $responseVehicle['warranty'] = $expiry->toDateString();
            }
    
            // Subscription
            if (!is_null($plan->subscription_duration)) {
                if ($request->subscription === "Expired") {
                    $expiry = $today->copy()->addMonths($plan->subscription_duration);
                } else {
                    $expiry = Carbon::parse($request->subscription)->addMonths($plan->subscription_duration);
                }
                $updatedFields['subscription'] = $expiry;
                $responseVehicle['subscription'] = $expiry->toDateString();
            }
    
            // AMC
            if (!is_null($plan->amc_duration)) {
                if ($request->amc === "Expired") {
                    $expiry = $today->copy()->addMonths($plan->amc_duration);
                } else {
                    $expiry = Carbon::parse($request->amc)->addMonths($plan->amc_duration);
                }
                $updatedFields['amc'] = $expiry;
                $responseVehicle['amc'] = $expiry->toDateString();
            }
    
            // Update vehicle
            if (!empty($updatedFields)) {
                $vehicle->update($updatedFields);
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Recharge submitted successfully.',
                'recharge' => $recharge,
                'vehicle' => $responseVehicle
            ], Response::HTTP_OK);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function UserRecharge(Request $request)
{
    try {

        $request->validate([
            'user_id'            => 'required|integer',
            'vehicle_number'     => 'required|string',
            'select_recharge_id' => 'required|integer',
            'payment_method'     => 'required|string',
            'payment_status'     => 'required|string',
            'payment_amount'     => 'required|numeric',
            'payment_id'         => 'required|string',
            'created_by_id'      => 'required|integer',
        ]);

        // fetch the vehicle with product model
        $vehicle = AddCustomerVehicle::with(['product_master.product_model'])
            ->where('vehicle_number', $request->vehicle_number)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'status' => false,
                'message' => "Vehicle not found"
            ]);
        }

        // find recharge plan
        $plan = RechargePlan::find($request->select_recharge_id);

        if (!$plan) {
            return response()->json([
                'status' => false,
                'message' => "Recharge plan not found"
            ]);
        }

        // Check if successful recharge exists
        $hasRecharge = RechargeRequest::where('vehicle_number', $vehicle->vehicle_number)
            ->whereIn('payment_status', ['success', 'completed', 'paid'])
            ->exists();

        $productModel = $vehicle->product_master?->product_model;

        $today = Carbon::now();


        /** -----------------------------------------------------
         * Get Existing BASE Expiry Dates
         * ----------------------------------------------------- */

        if ($hasRecharge) {

            // use existing expiry stored in vehicle
            $baseWarranty     = $vehicle->warranty     ? Carbon::parse($vehicle->warranty) : $today;
            $baseSubscription = $vehicle->subscription ? Carbon::parse($vehicle->subscription) : $today;
            $baseAmc          = $vehicle->amc          ? Carbon::parse($vehicle->amc) : $today;

        } else {

            // first time auto expiry based on request_date
            $requestDate = $vehicle->request_date
                ? Carbon::createFromFormat('d-m-Y', $vehicle->request_date)
                : $today;

            $baseWarranty     = $requestDate->copy()->addMonths($productModel->warranty ?? 0);
            $baseSubscription = $requestDate->copy()->addMonths($productModel->subscription ?? 0);
            $baseAmc          = $requestDate->copy()->addMonths($productModel->amc ?? 0);
        }


        /** -----------------------------------------------------
         * Calculate NEW EXPIRY
         * ----------------------------------------------------- */

        $newWarranty     = $plan->warranty_duration     ? $baseWarranty->copy()->addMonths($plan->warranty_duration) : $baseWarranty;
        $newSubscription = $plan->subscription_duration ? $baseSubscription->copy()->addMonths($plan->subscription_duration) : $baseSubscription;
        $newAmc          = $plan->amc_duration          ? $baseAmc->copy()->addMonths($plan->amc_duration) : $baseAmc;


        /** -----------------------------------------------------
         * Create Recharge entry
         * ----------------------------------------------------- */

        RechargeRequest::create([
            'user_id'            => $request->user_id,
            'vehicle_number'     => $request->vehicle_number,
            'select_recharge_id' => $request->select_recharge_id,
            'payment_method'     => $request->payment_method,
            'payment_status'     => $request->payment_status,
            'payment_amount'     => $request->payment_amount,
            'payment_date'       => now(),
            'payment_id'         => $request->payment_id,
            'created_by_id'      => $request->created_by_id,
        ]);


        /** -----------------------------------------------------
         * Update Vehicle expiry
         * ----------------------------------------------------- */

        $vehicle->update([
            'warranty'     => $newWarranty,
            'subscription' => $newSubscription,
            'amc'          => $newAmc,
        ]);


        return response()->json([
            'status' => true,
            'message' => 'Recharge Successful',
            'expiry' => [
                'warranty'     => $newWarranty->toDateString(),
                'subscription' => $newSubscription->toDateString(),
                'amc'          => $newAmc->toDateString(),
            ]
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'status' => false,
            'message' => 'Error occurred',
            'error' => $e->getMessage()
        ], 500);
    }
}



    
    public function getRechargeHistoryByUser($user_id)
    {
        try {
            // Check if user exists
            if (!\App\Models\User::find($user_id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.',
                ], Response::HTTP_NOT_FOUND);
            }
    
            $recharges = RechargeRequest::with(['product', 'select_recharge'])
                ->where('user_id', $user_id)
                ->orderBy('created_at', 'desc')
                ->get();
    
            return response()->json([
                'status' => true,
                'message' => 'Recharge history fetched successfully.',
                'data' => RechargeRequestResource::collection($recharges),
            ], Response::HTTP_OK);
    
        } catch (\Exception $e) {
            \Log::error('Recharge History Error', [
                'error' => $e->getMessage(),
                'user_id' => $user_id,
            ]);
    
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while fetching recharge history.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



}
