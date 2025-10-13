<?php

namespace App\Http\Requests;

use App\Models\RechargeRequest;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreRechargeRequestRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('recharge_request_create');
    }

    public function rules()
    {
        return [
            'vehicle_id' => [
               
                'nullable',
            ],
        ];
    }
}
