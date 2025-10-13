<?php

namespace App\Http\Requests;

use App\Models\StockTransfer;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreStockTransferRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('stock_transfer_create');
    }

public function rules()
{
    return [
        'transfer_date' => [
            'required',
            'date_format:' . config('panel.date_format'),
        ],
        'select_user_id' => [
            'required',
            'integer',
        ],
        'reseller_id' => [
            'nullable',
            'integer',
        ],
        'products' => [
            'required',
            'array',
        ],
        'products.*.product_id' => [
            'required',
            'integer',
            'exists:products,id', // Ensure product exists in the database
        ],
        'products.*.warranty' => [
            'nullable',
            'string',
            'max:255',
        ],
        'products.*.amc' => [
            'nullable',
            'string',
            'max:255',
        ],
        'products.*.mrp' => [
            'nullable',
            'numeric',
        ],
        'products.*.role_price' => [
            'nullable',
            'numeric',
        ],
        'products.*.discount_type' => [
            'nullable',
            'in:value,percent',  // Ensure only "value" or "percent" is allowed
        ],
        'products.*.discount_value' => [
            'nullable',
            'numeric',
        ],
        'products.*.final_price' => [
            'nullable',
            'numeric',
        ],
        'team_id' => [
            'nullable',
            'integer',
        ],
    ];
}


}
