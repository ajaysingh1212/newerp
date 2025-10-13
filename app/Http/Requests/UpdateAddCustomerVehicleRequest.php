<?php

namespace App\Http\Requests;

use App\Models\AddCustomerVehicle;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateAddCustomerVehicleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('add_customer_vehicle_edit');
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
                'unique:add_customer_vehicles,vehicle_number,' . request()->route('add_customer_vehicle')->id,
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
               
            ],
            'insurance.*' => [
               
            ],
            'pollution' => [
                
            ],
            'pollution.*' => [
               
            ],
            'registration_certificate' => [
               
            ],
            'registration_certificate.*' => [
                
            ],
            'vehicle_color' => [
                'string',
                'nullable',
            ],
            'id_proofs' => [
                'required',
            ],
            'status' => [
                
            ],
        ];
    }
}
