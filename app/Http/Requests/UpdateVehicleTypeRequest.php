<?php

namespace App\Http\Requests;

use App\Models\VehicleType;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateVehicleTypeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('vehicle_type_edit');
    }

    public function rules()
    {
        return [
            'vehicle_type' => [
                'string',
                'required',
            ],
            'vehicle_icon' => [
                'required',
            ],
        ];
    }
}
