<?php

namespace App\Http\Requests;

use App\Models\WithdrawalRequest;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateWithdrawalRequestRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('withdrawal_request_edit');
    }

    public function rules()
    {
        return [
            'select_investor_id' => [
                'required',
                'integer',
            ],
            'investment_id' => [
                'required',
                'integer',
            ],
            'amount' => [
                'required',
            ],
            'type' => [
                'required',
            ],
            'processing_hours' => [
                'string',
                'nullable',
            ],
            'requested_at' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'approved_at' => [
                'string',
                'nullable',
            ],
            'notes' => [
                'required',
            ],
        ];
    }
}
