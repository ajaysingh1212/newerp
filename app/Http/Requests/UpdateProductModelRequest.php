<?php

namespace App\Http\Requests;

use App\Models\ProductModel;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateProductModelRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('product_model_edit');
    }

    public function rules()
    {
        return [
            'product_model' => [
                'string',
                'required',
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
            'mrp' => [
                'string',
                'required',
            ],
            'cnf_price' => [
                'string',
                'required',
            ],
            'distributor_price' => [
                'string',
                'required',
            ],
            'dealer_price' => [
                'string',
                'required',
            ],
            'customer_price' => [
                'string',
                'required',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
