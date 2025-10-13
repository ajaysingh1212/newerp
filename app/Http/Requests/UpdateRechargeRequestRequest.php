<?php

namespace App\Http\Requests;

use App\Models\RechargeRequest;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateRechargeRequestRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('recharge_request_edit');
    }

    public function rules()
    {
        return [
            'vehicle_number' => [
                'string',
                'required',
            ],
        ];
    }
}
