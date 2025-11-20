<?php

namespace App\Http\Requests;

use App\Models\MonthlyPayoutRecord;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateMonthlyPayoutRecordRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('monthly_payout_record_edit');
    }

    public function rules()
    {
        return [
            'investment_id' => [
                'required',
                'integer',
            ],
            'investor_id' => [
                'required',
                'integer',
            ],
            'secure_interest_amount' => [
                'string',
                'required',
            ],
            'market_interest_amount' => [
                'string',
                'required',
            ],
            'total_payout_amount' => [
                'required',
            ],
            'month_for' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
        ];
    }
}
