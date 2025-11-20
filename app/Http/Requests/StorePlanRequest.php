<?php

namespace App\Http\Requests;

use App\Models\Plan;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StorePlanRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('plan_create');
    }

    public function rules()
    {
        return [
            'plan_name' => [
                'string',
                'required',
            ],
            'secure_interest_percent' => [
                'string',
                'required',
            ],
            'market_interest_percent' => [
                'string',
                'required',
            ],
            'total_interest_percent' => [
                'string',
                'required',
            ],
            'payout_frequency' => [
                'required',
            ],
            'min_invest_amount' => [
                'required',
            ],
            'max_invest_amount' => [
                'required',
            ],
            'lockin_days' => [
                'string',
                'required',
            ],
            'withdraw_processing_hours' => [
                'string',
                'required',
            ],
        ];
    }
}
