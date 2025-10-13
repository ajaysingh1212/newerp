<?php

namespace App\Http\Requests;

use App\Models\User;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('user_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'company_name' => [
               
            ],
            'email' => [
                'required',
                'unique:users,email,' . request()->route('user')->id,
            ],
            'gst_number' => [
                
            ],
            'date_inc' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'date_joining' => [
                'string',
                'nullable',
            ],
            'mobile_number' => [
                'string',
                'required',
            ],
            'whatsapp_number' => [
                'string',
                'nullable',
            ],
            'state_id' => [
               
            ],
            'district_id' => [
                
            ],
            'pin_code' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'bank_name' => [
                'string',
                'nullable',
            ],
            'branch_name' => [
                'string',
                'nullable',
            ],
            'ifsc' => [
                'string',
                'nullable',
            ],
            'ac_holder_name' => [
                'string',
                'nullable',
            ],
            'pan_number' => [
                'string',
                'nullable',
            ],
            'roles.*' => [
                'integer',
            ],
            'roles' => [
                'required',
                'array',
            ],
        ];
    }
}
