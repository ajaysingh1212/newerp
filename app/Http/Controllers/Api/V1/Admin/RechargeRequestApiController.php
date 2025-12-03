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


        /** ALWAYS save recharge entry */
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


        /** failed payment => STOP */
        if(!in_array(strtolower($request->payment_status),['success','completed','paid'])){
            return response()->json([
                'status'=>true,  // ⭐ IMPORTANT
                'message'=>'Recharge saved but payment failed.'
            ]);
        }




        /* SUCCESS LOGIC FROM HERE */

        $today = Carbon::now();
        $model = $vehicle->product_master?->product_model;


        /** CHECK recharge history */
        $hasRechargeBefore = RechargeRequest::where('vehicle_number',$vehicle->vehicle_number)
            ->whereIn('payment_status',['success','completed','paid'])
            ->count() > 1; // 1 is current request, means before existed also


        /** ------ BASE CALC ------ */

        if(!$hasRechargeBefore)
        {
            /** FIRST RECHARGE = activation + model duration */

            $reqDate = $vehicle->request_date
                ? Carbon::createFromFormat('d-m-Y',$vehicle->request_date)
                : $today;

            $baseWarranty     = $model?->warranty     ? $reqDate->copy()->addMonths($model->warranty)     : null;
            $baseSubscription = $model?->subscription ? $reqDate->copy()->addMonths($model->subscription) : null;
            $baseAmc          = $model?->amc          ? $reqDate->copy()->addMonths($model->amc)          : null;
        }
        else
        {
            /** NEXT RECHARGE = current expiry from vehicle table */

            $baseWarranty     = $vehicle->warranty     ? Carbon::parse($vehicle->warranty)     : null;
            $baseSubscription = $vehicle->subscription ? Carbon::parse($vehicle->subscription) : null;
            $baseAmc          = $vehicle->amc          ? Carbon::parse($vehicle->amc)          : null;
        }


        /** ----- APPLY PLAN ONLY ON THE FIELD ENABLED IN PLAN ----- */

        if($plan->warranty_duration > 0){
            $baseWarranty = ($baseWarranty && $baseWarranty->gt($today)) ? $baseWarranty : $today;
            $baseWarranty = $baseWarranty->copy()->addMonths($plan->warranty_duration);
        }

        if($plan->subscription_duration > 0){
            $baseSubscription = ($baseSubscription && $baseSubscription->gt($today)) ? $baseSubscription : $today;
            $baseSubscription = $baseSubscription->copy()->addMonths($plan->subscription_duration);
        }

        if($plan->amc_duration > 0){
            $baseAmc = ($baseAmc && $baseAmc->gt($today)) ? $baseAmc : $today;
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



public function CustomerRecharge(Request $request)
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

        /** CREATE RECHARGE ENTRY */
        $recharge = RechargeRequest::create([
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

        /* ⭐⭐⭐ COMMISSION LOGIC START ⭐⭐⭐ */
        $commissionData = [
            'recharge_request_id'      => $recharge->id,
            'customer_id'              => $request->user_id,
            'dealer_id'                => null,
            'distributor_id'           => null,
            'vehicle_id'               => $vehicle->id ?? null,
            'dealer_commission'        => 0,   // ⭐ NULL की जगह 0
            'distributor_commission'   => 0,   // ⭐ NULL की जगह 0
        ];

        $creator = \App\Models\User::with('roles')->find($request->created_by_id);

        if ($creator) {
            $roleIds = $creator->roles->pluck('id')->toArray();

            $amount = $request->payment_amount;
            $commissionValue = ($amount * 20) / 100; // 20%

            if (in_array(4, $roleIds)) { 
                // Dealer
                $commissionData['dealer_id'] = $creator->id;
                $commissionData['dealer_commission'] = $commissionValue;
            } 
            else if (in_array(5, $roleIds)) { 
                // Distributor
                $commissionData['distributor_id'] = $creator->id;
                $commissionData['distributor_commission'] = $commissionValue;
            }
        }

        \App\Models\Commission::create($commissionData);
        /* ⭐⭐⭐ COMMISSION LOGIC END ⭐⭐⭐ */


        /** failed payment => STOP */
        if(!in_array(strtolower($request->payment_status),['success','completed','paid'])){
            return response()->json([
                'status'=>true,
                'message'=>'Recharge saved but payment failed.'
            ]);
        }

        /* ------------------------------
            BELOW LOGIC REMAINS SAME
        ------------------------------- */

        $today = Carbon::now();
        $model = $vehicle->product_master?->product_model;

        $hasRechargeBefore = RechargeRequest::where('vehicle_number',$vehicle->vehicle_number)
            ->whereIn('payment_status',['success','completed','paid'])
            ->count() > 1;

        if(!$hasRechargeBefore)
        {
            $reqDate = $vehicle->request_date
                ? Carbon::createFromFormat('d-m-Y',$vehicle->request_date)
                : $today;

            $baseWarranty     = $model?->warranty     ? $reqDate->copy()->addMonths($model->warranty)     : null;
            $baseSubscription = $model?->subscription ? $reqDate->copy()->addMonths($model->subscription) : null;
            $baseAmc          = $model?->amc          ? $reqDate->copy()->addMonths($model->amc)          : null;
        }
        else
        {
            $baseWarranty     = $vehicle->warranty     ? Carbon::parse($vehicle->warranty)     : null;
            $baseSubscription = $vehicle->subscription ? Carbon::parse($vehicle->subscription) : null;
            $baseAmc          = $vehicle->amc          ? Carbon::parse($vehicle->amc)          : null;
        }

        if($plan->warranty_duration > 0){
            $baseWarranty = ($baseWarranty && $baseWarranty->gt($today)) ? $baseWarranty : $today;
            $baseWarranty = $baseWarranty->copy()->addMonths($plan->warranty_duration);
        }

        if($plan->subscription_duration > 0){
            $baseSubscription = ($baseSubscription && $baseSubscription->gt($today)) ? $baseSubscription : $today;
            $baseSubscription = $baseSubscription->copy()->addMonths($plan->subscription_duration);
        }

        if($plan->amc_duration > 0){
            $baseAmc = ($baseAmc && $baseAmc->gt($today)) ? $baseAmc : $today;
            $baseAmc = $baseAmc->copy()->addMonths($plan->amc_duration);
        }

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

    } catch (\Exception $e) {
        return response()->json([
            'status'=>false,
            'message'=>'Error Occurred',
            'error'=>$e->getMessage()
        ],500);
    }
}




public function getCommissionAmount($user_id)
{
    try {

        // Find user with roles
        $user = \App\Models\User::with('roles')->find($user_id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        $roleIds = $user->roles->pluck('id')->toArray();

        $earned = 0;
        $redeemed = 0;

        /* -------------------------------
           CHECK ROLE → Dealer or Distributor
        -------------------------------- */

        // Dealer (role_id = 4)
        if (in_array(4, $roleIds)) {

            // Total commission earned as Dealer
            $earned = \App\Models\Commission::where('dealer_id', $user_id)
                        ->sum('dealer_commission');

        }

        // Distributor (role_id = 5)
        else if (in_array(5, $roleIds)) {

            // Total commission earned as Distributor
            $earned = \App\Models\Commission::where('distributor_id', $user_id)
                        ->sum('distributor_commission');
        }

        /* -------------------------------
           TOTAL REDEEM DONE BY USER
           RechargeRequest → redeem_amount
           created_by_id = user_id
        -------------------------------- */

        $redeemed = \App\Models\RechargeRequest::where('created_by_id', $user_id)
                    ->whereNotNull('redeem_amount')
                    ->sum('redeem_amount');

        /* -------------------------------
           FINAL COMMISSION = earned - redeemed
        -------------------------------- */

        $finalAmount = $earned - $redeemed;

        return response()->json([
            'status' => true,
            'user_id' => $user_id,
            'role' => in_array(4, $roleIds) ? 'Dealer' : (in_array(5, $roleIds) ? 'Distributor' : 'None'),
            'earned_commission' => round($earned, 2),
            'redeemed_amount' => round($redeemed, 2),
            'final_commission' => round($finalAmount, 2)
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'status' => false,
            'message' => 'Error Occurred',
            'error' => $e->getMessage()
        ], 500);
    }
}



public function getCommissionHistory($user_id)
{
    try {

        // Fetch user with roles
        $user = \App\Models\User::with('roles')->find($user_id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        $roleIds = $user->roles->pluck('id')->toArray();

        $isDealer = in_array(4, $roleIds);
        $isDistributor = in_array(5, $roleIds);

        if (!$isDealer && !$isDistributor) {
            return response()->json([
                'status' => false,
                'message' => 'No commission available for this user.'
            ], 400);
        }

        // Fetch commission history
        $commissions = \App\Models\Commission::with(['rechargeRequest'])
            ->when($isDealer, fn($q) => $q->where('dealer_id', $user_id))
            ->when($isDistributor, fn($q) => $q->where('distributor_id', $user_id))
            ->orderBy('id', 'desc')
            ->get();

        $history = [];

        foreach ($commissions as $c) {

            $recharge = $c->rechargeRequest;
            if (!$recharge) continue;

            $earned = $isDealer ? $c->dealer_commission : $c->distributor_commission;

            // If redeem happened on this recharge, fetch redeem value
            $redeemed = $recharge->redeem_amount ?? 0;

            $history[] = [
                'recharge_request_id'  => $recharge->id,
                'vehicle_number'       => $recharge->vehicle_number,
                'payment_amount'       => $recharge->payment_amount,
                'payment_status'       => $recharge->payment_status,
                'payment_date'         => $recharge->payment_date,

                // ⭐ NEW FIELDS
                'earned_commission'    => round($earned, 2),
                'redeemed_commission'  => round($redeemed, 2),
            ];
        }

        return response()->json([
            'status' => true,
            'user_id' => $user_id,
            'role' => $isDealer ? 'Dealer' : 'Distributor',
            'history' => $history
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'status' => false,
            'message' => 'Error Occurred',
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
