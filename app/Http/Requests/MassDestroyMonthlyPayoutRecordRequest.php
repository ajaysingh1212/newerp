<?php

namespace App\Http\Requests;

use App\Models\MonthlyPayoutRecord;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyMonthlyPayoutRecordRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('monthly_payout_record_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:monthly_payout_records,id',
        ];
    }
}
