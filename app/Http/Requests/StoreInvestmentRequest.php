<?php

namespace App\Http\Requests;

use App\Models\Investment;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreInvestmentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('investment_create');
    }

    public function rules()
    {
        return [
            'select_investor_id' => [
                'required',
                'integer',
            ],
            'principal_amount' => [
                'required',
            ],
            'secure_interest_percent' => [
                'string',
                'nullable',
            ],
            'market_interest_percent' => [
                'string',
                'nullable',
            ],
            'total_interest_percent' => [
                'string',
                'nullable',
            ],
            'start_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
        ];
    }
}
