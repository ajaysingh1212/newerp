<?php

namespace App\Http\Requests;

use App\Models\ProductMaster;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateProductMasterRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('product_master_edit');
    }

    public function rules()
    {
        return [
            'imei_id' => [
                'required',
                'integer',
            ],
            'warranty' => [
                'string',
                'required',
            ],
            'subscription' => [
                'string',
                'required',
            ],
            'amc' => [
                'string',
                'required',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
