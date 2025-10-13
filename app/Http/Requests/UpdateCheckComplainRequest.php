<?php

namespace App\Http\Requests;

use App\Models\CheckComplain;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCheckComplainRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('check_complain_edit');
    }

    public function rules()
    {
        return [
            'ticket_number' => [
                'string',
                'required',
                'unique:check_complains,ticket_number,' . request()->route('check_complain')->id,
            ],
     
            'vehicle_no' => [
                
            ],
            'customer_name' => [
               
            ],
            'phone_number' => [
               
            ],
            'reason' => [
                'required',
            ],
  
            'attechment' => [
                'array',
            ],
        ];
    }
}
