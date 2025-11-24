<?php

namespace App\Http\Requests;

use App\Models\Registration;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreRegistrationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('registration_create');
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
                'unique:registrations',
            ],
            'pan_number' => [
                'string',
                'min:10',
                'max:10',
                'required',
                'unique:registrations',
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
            'address_line_1' => [
                'required',
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
                'unique:registrations',
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
            'pan_card_image' => [
                'array',
                'required',
            ],
            'pan_card_image.*' => [
                'required',
            ],
            'aadhaar_front_image' => [
                'required',
            ],
            'aadhaar_back_image' => [
                'required',
            ],
            'profile_image' => [
                'array',
            ],
            'signature_image' => [
                'array',
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
