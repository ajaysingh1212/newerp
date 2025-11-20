<?php

namespace App\Http\Requests;

use App\Models\Investment;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateInvestmentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('investment_edit');
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
            'lockin_end_date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'next_payout_date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
        ];
    }
}
