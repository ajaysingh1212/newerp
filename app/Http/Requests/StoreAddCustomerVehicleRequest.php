<?php

namespace App\Http\Requests;

use App\Models\AddCustomerVehicle;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreAddCustomerVehicleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('add_customer_vehicle_create');
    }

    public function rules()
    {
        return [
            'select_vehicle_type_id' => [
                'required',
                'integer',
            ],
            'vehicle_number' => [
                'string',
                'required',
                'unique:add_customer_vehicles',
            ],
            'owners_name' => [
                'string',
                'required',
            ],
            'insurance_expiry_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'chassis_number' => [
                'string',
                'nullable',
            ],
            'vehicle_model' => [
                'string',
                'nullable',
            ],
            'owner_image' => [
                'string',
                'nullable',
            ],
            'insurance' => [
                'array',
                'nullable',
            ],
            'insurance.*' => [
                'nullable',
            ],
            'pollution' => [
                'array',
                'nullable',
            ],
            'pollution.*' => [
                'nullable',
            ],
            'registration_certificate' => [
                'array',
                'nullable',
            ],
            'registration_certificate.*' => [
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
