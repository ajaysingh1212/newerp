<?php

namespace App\Http\Requests;

use App\Models\ImeiModel;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateImeiModelRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('imei_model_edit');
    }

    public function rules()
    {
        return [
            'imei_model_number' => [
                'string',
                'required',
                'unique:imei_models,imei_model_number,' . request()->route('imei_model')->id,
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
