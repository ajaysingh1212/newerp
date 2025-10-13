<?php

namespace App\Http\Requests;

use App\Models\CurrentStock;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCurrentStockRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('current_stock_edit');
    }

    public function rules()
    {
        return [
            'sku' => [
                'string',
                'required',
                'unique:current_stocks,sku,' . request()->route('current_stock')->id,
            ],
            'product_name' => [
                'string',
                'required',
            ],
        ];
    }
}
