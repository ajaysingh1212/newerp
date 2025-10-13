<?php

namespace App\Http\Requests;

use App\Models\CheckPartyStock;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCheckPartyStockRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('check_party_stock_edit');
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
