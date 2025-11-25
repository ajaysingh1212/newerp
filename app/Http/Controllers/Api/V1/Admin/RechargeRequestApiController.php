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
    
    
    
    
    public function UserRecharge(Request $request)
{
    try {

        $request->validate([
            'user_id'            => 'required|integer',
            'vehicle_number'     => 'required|string',
            'select_recharge_id' => 'required|integer',
            'payment_status'     => 'required|string',
            'payment_amount'     => 'required|numeric',
            'created_by_id'      => 'required|integer',
            'razorpay_payment_id'=> 'nullable|string',
        ]);

        $vehicle = AddCustomerVehicle::with(['product_master.product_model'])
            ->where('vehicle_number',$request->vehicle_number)
            ->first();

        if(!$vehicle){
            return response()->json([
                'status'=>false,
                'message'=>"Vehicle not found"
            ]);
        }

        $plan = RechargePlan::find($request->select_recharge_id);

        if(!$plan){
            return response()->json([
                'status'=>false,
                'message'=>"Recharge plan not found"
            ]);
        }


        /** Always record recharge */
        RechargeRequest::create([
            'user_id'            => $request->user_id,
            'vehicle_number'     => $request->vehicle_number,
            'select_recharge_id' => $request->select_recharge_id,
            'notes'              => strtoupper($request->vehicle_number).', '.$plan->plan_name.' From Mobile',
            'payment_method'     => 'online',
            'payment_status'     => strtolower($request->payment_status),
            'payment_amount'     => $request->payment_amount,
            'payment_date'       => now(),
            'payment_id'         => 'TXN'.rand(1000000000,9999999999),
            'razorpay_payment_id'=> $request->razorpay_payment_id,
            'created_by_id'      => $request->created_by_id,
        ]);


        if(!in_array(strtolower($request->payment_status),['success','completed','paid']))
        {
            return response()->json([
                'status'=>true,
                'message'=>'Recharge saved but payment failed — no expiry update.'
            ]);
        }


        /**  ---- SUCCESS CASE ---- **/

        $today = Carbon::now();

        $model = $vehicle->product_master?->product_model;


        /** GET CURRENT CORRECT EXPIRY EXACT LIKE VEHICLE LIST API */

        // warranty base
        if($vehicle->warranty){
            $baseWarranty = Carbon::parse($vehicle->warranty);
        } else {
            $requestDate = Carbon::createFromFormat('d-m-Y',$vehicle->request_date);
            $baseWarranty = $model?->warranty ? $requestDate->copy()->addMonths($model->warranty) : null;
        }

        // subscription base
        if($vehicle->subscription){
            $baseSubscription = Carbon::parse($vehicle->subscription);
        } else {
            $requestDate = Carbon::createFromFormat('d-m-Y',$vehicle->request_date);
            $baseSubscription = $model?->subscription ? $requestDate->copy()->addMonths($model->subscription) : null;
        }

        // amc base
        if($vehicle->amc){
            $baseAmc = Carbon::parse($vehicle->amc);
        } else {
            $requestDate = Carbon::createFromFormat('d-m-Y',$vehicle->request_date);
            $baseAmc = $model?->amc ? $requestDate->copy()->addMonths($model->amc) : null;
        }


        /** Now ONLY extend the ONE which exists in PLAN */

        if($plan->warranty_duration > 0){
            $baseWarranty = ($baseWarranty && $baseWarranty->gt($today))
                ? $baseWarranty
                : $today;
            $baseWarranty = $baseWarranty->copy()->addMonths($plan->warranty_duration);
        }

        if($plan->subscription_duration > 0){
            $baseSubscription = ($baseSubscription && $baseSubscription->gt($today))
                ? $baseSubscription
                : $today;
            $baseSubscription = $baseSubscription->copy()->addMonths($plan->subscription_duration);
        }

        if($plan->amc_duration > 0){
            $baseAmc = ($baseAmc && $baseAmc->gt($today))
                ? $baseAmc
                : $today;
            $baseAmc = $baseAmc->copy()->addMonths($plan->amc_duration);
        }


        /** UPDATE VEHICLE */
        $vehicle->update([
            'warranty'     => $baseWarranty,
            'subscription' => $baseSubscription,
            'amc'          => $baseAmc,
        ]);


        return response()->json([
            'status'=>true,
            'message'=>'Recharge Successful',
            'expiry'=>[
                'warranty'=>$baseWarranty?->toDateString(),
                'subscription'=>$baseSubscription?->toDateString(),
                'amc'=>$baseAmc?->toDateString(),
            ]
        ]);


    } catch (\Exception $e){

        return response()->json([
            'status'=>false,
            'message'=>'Error Occurred',
            'error'=>$e->getMessage()
        ],500);
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
