<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\RedeemCode;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RedeemCodeApiController extends Controller
{
    /**
     * Check Redeem Code
     */
    public function checkRedeemCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'recharge_plan_id' => 'nullable|integer',
        ]);

        $code = trim($request->code);

        // Find redeem code
        $redeemCode = RedeemCode::with('rechargePlan')
            ->where('code', $code)
            ->first();

        // Code not found
        if (!$redeemCode) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Invalid redeem code.',
            ], 404);
        }

        // Check status
        if ($redeemCode->status != 1) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'This redeem code is inactive.',
                'data' => [
                    'code' => $redeemCode->code,
                    'status' => $redeemCode->status,
                    'use_status' => $redeemCode->use_status,
                ],
            ], 422);
        }

        // Check already used
        if ($redeemCode->use_status == 1) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'This redeem code has already been used.',
                'data' => [
                    'code' => $redeemCode->code,
                    'use_status' => $redeemCode->use_status,
                    'used_by_id' => $redeemCode->used_by_id,
                    'used_at' => $redeemCode->used_at,
                ],
            ], 422);
        }

        // Check expiry
        if (
            !empty($redeemCode->valid_up_to) &&
            Carbon::parse($redeemCode->valid_up_to)->isPast()
        ) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'This redeem code has expired.',
                'data' => [
                    'code' => $redeemCode->code,
                    'valid_up_to' => $redeemCode->valid_up_to,
                ],
            ], 422);
        }

        // Optional plan validation
        if (
            $request->filled('recharge_plan_id') &&
            $redeemCode->recharge_plan_id != $request->recharge_plan_id
        ) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'This redeem code is not valid for the selected recharge plan.',
                'data' => [
                    'redeem_plan_id' => $redeemCode->recharge_plan_id,
                    'selected_plan_id' => $request->recharge_plan_id,
                ],
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Recharge Plan Details
        |--------------------------------------------------------------------------
        */

        $plan = $redeemCode->rechargePlan;

        $planPrice = 0;

        if ($plan) {
            // Change "price" if your recharge_plans table uses another column.
            $planPrice = (float) ($plan->price ?? 0);
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate Discount
        |--------------------------------------------------------------------------
        */

        $discountAmount = $redeemCode->calculateDiscount($planPrice);

        $payableAmount = max($planPrice - $discountAmount, 0);

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'valid' => true,
            'message' => 'Redeem code is valid.',

            'data' => [

                // Redeem Code
                'redeem_code' => [
                    'id' => $redeemCode->id,
                    'code' => $redeemCode->code,
                    'status' => $redeemCode->status,
                    'use_status' => $redeemCode->use_status,
                    'valid_up_to' => $redeemCode->valid_up_to,
                    'used_by_id' => $redeemCode->used_by_id,
                    'used_at' => $redeemCode->used_at,
                ],

                // Discount
                'discount' => [
                    'type' => $redeemCode->discount_type,
                    'value' => (float) $redeemCode->discount_value,
                    'amount' => $discountAmount,
                ],

                // Recharge Plan
                'recharge_plan' => $plan ? [
                    'id' => $plan->id,
                    'name' => $plan->name ?? null,
                    'price' => $planPrice,
                    'details' => $plan,
                ] : null,

                // Final Amount
                'amount' => [
                    'plan_price' => $planPrice,
                    'discount_amount' => $discountAmount,
                    'payable_amount' => $payableAmount,
                ],
            ],
        ]);
    }
}