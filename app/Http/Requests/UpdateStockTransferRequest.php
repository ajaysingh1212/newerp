<?php

namespace App\Http\Requests;

use App\Models\StockTransfer;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateStockTransferRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('stock_transfer_edit');
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
            'select_products.*' => [
                'integer',
            ],
            'select_products' => [
                'required',
                'array',
            ],
        ];
    }
}
