<?php

namespace App\Http\Requests;

use App\Models\Vt;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyVtRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('vt_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:vts,id',
        ];
    }
}
