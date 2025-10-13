<?php

namespace App\Http\Requests;

use App\Models\CurrentStock;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCurrentStockRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('current_stock_create');
    }

    public function rules()
    {
        return [
            'sku' => [
                'string',
                'required',
                'unique:current_stocks',
            ],
            'product_name' => [
                'string',
                'required',
            ],
        ];
    }
}
