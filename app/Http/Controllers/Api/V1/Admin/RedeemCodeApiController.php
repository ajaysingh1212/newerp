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
    'redeem_amount' => (float) $redeemCode->discount_amount,
]);
    }
}