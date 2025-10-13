<?php

namespace App\Http\Requests;

use App\Models\CurrentStock;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyCurrentStockRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('current_stock_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:current_stocks,id',
        ];
    }
}
