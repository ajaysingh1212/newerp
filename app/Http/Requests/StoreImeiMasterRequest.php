<?php

namespace App\Http\Requests;

use App\Models\ImeiMaster;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreImeiMasterRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('imei_master_create');
    }

    public function rules()
    {
        return [
            'imei_model_id' => [
                'required',
                'integer',
            ],
            'imei_number' => [
                'string',
                'required',
                'unique:imei_masters',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
