<?php

namespace App\Http\Requests;

use App\Models\InvestorTransaction;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyInvestorTransactionRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('investor_transaction_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:investor_transactions,id',
        ];
    }
}
