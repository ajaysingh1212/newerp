<?php

namespace App\Http\Requests;

use App\Models\ActivationRequest;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreActivationRequestRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('activation_request_create');
    }

    public function rules()
    {
        return [
            'party_type_id' => [
                'required',
                'integer',
            ],
            'select_party_id' => [
                'required',
                'integer',
            ],
            'product_id' => [
                'required',
                'integer',
            ],
            'customer_name' => [
                'string',
                'required',
            ],
            'mobile_number' => [
                'string',
                'required',
                
            ],
            'whatsapp_number' => [
                'string',
                'nullable',
            ],
            'request_date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'vehicle_model' => [
                'string',
                'required',
            ],
            'vehicle_reg_no' => [
                'string',
                'required',
            ],
            'chassis_number' => [
                'string',
                'nullable',
            ],
            'engine_number' => [
                'string',
                'nullable',
            ],
            'vehicle_color' => [
                'string',
                'nullable',
            ],
            'id_proofs' => [
                'required',
            ],
        ];
    }
}
