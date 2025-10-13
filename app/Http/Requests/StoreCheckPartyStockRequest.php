<?php

namespace App\Http\Requests;

use App\Models\CheckPartyStock;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCheckPartyStockRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('check_party_stock_create');
    }

    public function rules()
    {
        return [
            'select_parties.*' => [
                'integer',
            ],
            'select_parties' => [
                'array',
            ],
        ];
    }
}
