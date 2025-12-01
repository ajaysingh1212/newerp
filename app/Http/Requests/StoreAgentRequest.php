<?php

namespace App\Http\Requests;

use App\Models\Agent;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreAgentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('agent_create');
    }

    public function rules()
    {
        return [
            'full_name' => [
                'string',
                'required',
            ],
            'phone_number' => [
                'string',
                'required',
                'unique:agents',
            ],
            'whatsapp_number' => [
                'string',
                'nullable',
            ],
            'email' => [
                'string',
                'nullable',
            ],
            'pin_code' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'state' => [
                'string',
                'nullable',
            ],
            'city' => [
                'string',
                'nullable',
            ],
            'district' => [
                'string',
                'nullable',
            ],
            'additional_document' => [
                'array',
            ],
        ];
    }
}