<?php

namespace App\Http\Requests;

use App\Models\WithdrawalRequest;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyWithdrawalRequestRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('withdrawal_request_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:withdrawal_requests,id',
        ];
    }
}
