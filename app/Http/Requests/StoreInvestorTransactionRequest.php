<?php

namespace App\Http\Requests;

use App\Models\InvestorTransaction;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreInvestorTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('investor_transaction_create');
    }

    public function rules()
    {
        return [
            'investor_id' => [
                'required',
                'integer',
            ],
            'transaction_type' => [
                'required',
            ],
            'amount' => [
                'required',
            ],
        ];
    }
}
