<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\RedeemCode;
use App\Models\RechargePlan;
use Carbon\Carbon;

class RedeemCodeApiController extends Controller
{
    /**
     * Check Redeem Code with Recharge Plan
     *
     * GET /api/v1/redeem-code/{recharge_plan_id}/{code}
     */
    public function getRedeemCode($recharge_plan_id, $code)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Find Redeem Code
        |--------------------------------------------------------------------------
        */

        $redeemCode = RedeemCode::with([
            'rechargePlan',
            'usedBy',
            'rechargeRequest',
            'createdBy'
        ])
        ->where('code', $code)
        ->first();

        if (!$redeemCode) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Invalid redeem code.'
            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | 2. Check Deleted Code
        |--------------------------------------------------------------------------
        */

        if ($redeemCode->deleted_at !== null) {
            return response()->json([
                'success' => true,
                'valid' => false,
                'message' => 'This redeem code has been deleted.'
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 3. Check Status
        |--------------------------------------------------------------------------
        */

        if ($redeemCode->status !== 'active') {
            return response()->json([
                'success' => true,
                'valid' => false,
                'message' => 'This redeem code is inactive.'
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 4. Check Use Status
        |--------------------------------------------------------------------------
        */

        if ($redeemCode->use_status !== 'not_used') {
            return response()->json([
                'success' => true,
                'valid' => false,
                'message' => 'This redeem code has already been used.'
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 5. Check Used By
        |--------------------------------------------------------------------------
        */

        if ($redeemCode->used_by_id !== null) {
            return response()->json([
                'success' => true,
                'valid' => false,
                'message' => 'This redeem code has already been used by another user.'
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 6. Check Used At
        |--------------------------------------------------------------------------
        */

        if ($redeemCode->used_at !== null) {
            return response()->json([
                'success' => true,
                'valid' => false,
                'message' => 'This redeem code has already been redeemed.'
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 7. Check Recharge Plan ID
        |--------------------------------------------------------------------------
        */

        if ((int) $redeemCode->recharge_plan_id !== (int) $recharge_plan_id) {
            return response()->json([
                'success' => true,
                'valid' => false,
                'message' => 'This redeem code is not valid for the selected recharge plan.',
                'data' => [
                    'redeem_plan_id' => (int) $redeemCode->recharge_plan_id,
                    'requested_plan_id' => (int) $recharge_plan_id
                ]
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 8. Check Recharge Plan Exists
        |--------------------------------------------------------------------------
        */

        $rechargePlan = RechargePlan::where('id', $recharge_plan_id)
            ->whereNull('deleted_at')
            ->first();

        if (!$rechargePlan) {
            return response()->json([
                'success' => true,
                'valid' => false,
                'message' => 'Recharge plan not found or has been deleted.'
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 9. Check Valid Up To
        |--------------------------------------------------------------------------
        |
        | valid_up_to = 2026-08-12
        |
        | Means valid until:
        | 2026-08-12 23:59:59
        |
        */

        if ($redeemCode->valid_up_to !== null) {

            $validUpTo = Carbon::parse($redeemCode->valid_up_to)
                ->endOfDay();

            if (Carbon::now()->greaterThan($validUpTo)) {
                return response()->json([
                    'success' => true,
                    'valid' => false,
                    'message' => 'This redeem code has expired.',
                    'data' => [
                        'valid_up_to' => $redeemCode->valid_up_to
                    ]
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | 10. Discount Amount
        |--------------------------------------------------------------------------
        */

        $discountAmount = (float) $redeemCode->discount_amount;

        $planPrice = (float) $rechargePlan->price;

        $payableAmount = max(
            $planPrice - $discountAmount,
            0
        );


        /*
        |--------------------------------------------------------------------------
        | VALID
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'valid' => true,
            'message' => 'Redeem code is valid.',

            'data' => [

                /*
                |--------------------------------------------------------------------------
                | Redeem Code
                |--------------------------------------------------------------------------
                */

                'redeem_code' => [
                    'id' => $redeemCode->id,
                    'code' => $redeemCode->code,

                    'recharge_plan_id' => $redeemCode->recharge_plan_id,

                    'valid_up_to' => $redeemCode->valid_up_to,

                    'discount_type' => $redeemCode->discount_type,
                    'discount_value' => (float) $redeemCode->discount_value,

                    // Actual amount that will be deducted
                    'discount_amount' => $discountAmount,

                    'status' => $redeemCode->status,
                    'use_status' => $redeemCode->use_status,

                    'used_by_id' => $redeemCode->used_by_id,
                    'recharge_request_id' => $redeemCode->recharge_request_id,
                    'used_at' => $redeemCode->used_at,

                    'created_by_id' => $redeemCode->created_by_id,
                    'creator_name' => $redeemCode->creator_name,
                    'creator_role' => $redeemCode->creator_role,

                    'created_at' => $redeemCode->created_at,
                    'updated_at' => $redeemCode->updated_at,
                    'deleted_at' => $redeemCode->deleted_at,
                ],


                /*
                |--------------------------------------------------------------------------
                | Recharge Plan
                |--------------------------------------------------------------------------
                */

                'recharge_plan' => [
                    'id' => $rechargePlan->id,
                    'type' => $rechargePlan->type,
                    'plan_name' => $rechargePlan->plan_name,

                    'price' => (float) $rechargePlan->price,

                    'subscription_duration' =>
                        $rechargePlan->subscription_duration,

                    'amc_duration' =>
                        $rechargePlan->amc_duration,

                    'warranty_duration' =>
                        $rechargePlan->warranty_duration,

                    'discription' =>
                        $rechargePlan->discription,
                ],


                /*
                |--------------------------------------------------------------------------
                | Amount
                |--------------------------------------------------------------------------
                */

                'amount' => [
                    'plan_amount' => $planPrice,

                    // Amount which will be deducted
                    'discount_amount' => $discountAmount,

                    // Final amount customer will pay
                    'payable_amount' => $payableAmount,
                ]
            ]
        ]);
    }
}