<?php

namespace App\Http\Requests;

use App\Models\User;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('user_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'company_name' => [
                // 'string',
            ],
            'email' => [
                'required',
                'unique:users',
            ],
            'gst_number' => [
                // 'string',
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
                // 'required',
                // 'integer',
            ],
            'district_id' => [
                // 'integer',
            ],
            'pin_code' => [
                'nullable',
                'integer',
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
            'password' => [
                'required',
            ],
        ];
    }
}
