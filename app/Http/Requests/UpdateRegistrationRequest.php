<?php

namespace App\Http\Requests;

use App\Models\Registration;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateRegistrationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('registration_edit');
    }

    public function rules()
    {
        return [
            'reg' => [
                'string',
                'nullable',
            ],
            'investor_id' => [
                'required',
                'integer',
            ],
            'referral_code' => [
                'string',
                'nullable',
            ],
            'aadhaar_number' => [
                'string',
                'min:12',
                'max:12',
                'required',
                'unique:registrations,aadhaar_number,' . request()->route('registration')->id,
            ],
            'pan_number' => [
                'string',
                'min:10',
                'max:10',
                'required',
                'unique:registrations,pan_number,' . request()->route('registration')->id,
            ],
            'dob' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'gender' => [
                'required',
            ],
            'father_name' => [
                'string',
                'nullable',
            ],
       
            'pincode' => [
                'string',
                'min:6',
                'max:6',
                'required',
            ],
            'city' => [
                'string',
                'nullable',
            ],
            'state' => [
                'string',
                'nullable',
            ],
            'country' => [
                'string',
                'nullable',
            ],
            'bank_account_holder_name' => [
                'string',
                'required',
            ],
            'bank_account_number' => [
                'string',
                'required',
                'unique:registrations,bank_account_number,' . request()->route('registration')->id,
            ],
            'ifsc_code' => [
                'string',
                'required',
            ],
            'bank_name' => [
                'string',
                'nullable',
            ],
            'bank_branch' => [
                'string',
                'nullable',
            ],
    
            'income_range' => [
                'required',
            ],
            'occupation' => [
                'required',
            ],
        ];
    }
}
