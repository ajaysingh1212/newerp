<?php

namespace App\Http\Requests;

use App\Models\WithdrawalRequest;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreWithdrawalRequestRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('withdrawal_request_create');
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
            'requested_at' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'notes' => [
                'required',
            ],
        ];
    }
}
