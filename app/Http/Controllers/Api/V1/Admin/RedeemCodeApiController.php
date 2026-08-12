<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\RedeemCode;
use Carbon\Carbon;

class RedeemCodeApiController extends Controller
{
    /**
     * Check Redeem Code Details
     *
     * GET /api/v1/redeem-code/{code}
     */
    public function getRedeemCode($code)
    {
        $redeemCode = RedeemCode::with([
            'rechargePlan',
            'usedBy',
            'rechargeRequest',
            'createdBy'
        ])
        ->where('code', $code)
        ->first();

        // Code not found
        if (!$redeemCode) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Redeem code not found.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        $isActive = $redeemCode->status === 'active';
        $isNotUsed = $redeemCode->use_status === 'not_used';

        $isNotExpired = true;

        if ($redeemCode->valid_up_to) {
            $isNotExpired = Carbon::parse($redeemCode->valid_up_to)->isFuture();
        }

        $isValid = $isActive && $isNotUsed && $isNotExpired;

        /*
        |--------------------------------------------------------------------------
        | Recharge Plan
        |--------------------------------------------------------------------------
        */

        $plan = $redeemCode->rechargePlan;

        /*
        |--------------------------------------------------------------------------
        | Discount
        |--------------------------------------------------------------------------
        */

        $discountValue = (float) $redeemCode->discount_value;
        $discountAmount = (float) $redeemCode->discount_amount;

        /*
        |--------------------------------------------------------------------------
        | Final Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'valid' => $isValid,

            'message' => $isValid
                ? 'Redeem code is valid.'
                : 'Redeem code is not valid.',

            'data' => [

                // 🎟️ Redeem Code Details
                'redeem_code' => [
                    'id' => $redeemCode->id,
                    'code' => $redeemCode->code,
                    'recharge_plan_id' => $redeemCode->recharge_plan_id,

                    'valid_up_to' => $redeemCode->valid_up_to,

                    'discount_type' => $redeemCode->discount_type,
                    'discount_value' => $discountValue,
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
                ],

                // 💳 Recharge Plan Details
                'recharge_plan' => $plan,

                // 👤 Used By
                'used_by' => $redeemCode->usedBy,

                // 🧾 Recharge Request
                'recharge_request' => $redeemCode->rechargeRequest,

                // 👨‍💼 Created By
                'created_by' => $redeemCode->createdBy,

                // 🔍 Validation Details
                'validation' => [
                    'active' => $isActive,
                    'not_used' => $isNotUsed,
                    'not_expired' => $isNotExpired,
                ],
            ],
        ]);
    }
}