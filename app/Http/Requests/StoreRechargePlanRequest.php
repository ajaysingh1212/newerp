<?php

namespace App\Http\Requests;

use App\Models\RechargePlan;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreRechargePlanRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('recharge_plan_create');
    }

    public function rules()
    {
        return [
            'type' => [
                'string',
                'required',
            ],
            'plan_name' => [
                'string',
                'required',
            ],
            'amc_duration' => [
                'string',
                'nullable',
            ],
            'warranty_duration' => [
                'string',
                'nullable',
            ],
            'subscription_duration' => [
                'string',
                'nullable',
            ],
            'price' => [
                'string',
                'required',
            ],
        ];
    }
}