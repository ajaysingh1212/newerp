<?php

namespace App\Http\Requests;

use App\Models\AddCustomerVehicle;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyAddCustomerVehicleRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('add_customer_vehicle_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:add_customer_vehicles,id',
        ];
    }
}
