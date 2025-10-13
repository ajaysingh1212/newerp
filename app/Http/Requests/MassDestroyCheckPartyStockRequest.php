<?php

namespace App\Http\Requests;

use App\Models\CheckPartyStock;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyCheckPartyStockRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('check_party_stock_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:check_party_stocks,id',
        ];
    }
}
