<?php

namespace App\Http\Requests;

use App\Models\ImeiModel;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreImeiModelRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('imei_model_create');
    }

    public function rules()
    {
        return [
            'imei_model_number' => [
                'string',
                'required',
                'unique:imei_models',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
