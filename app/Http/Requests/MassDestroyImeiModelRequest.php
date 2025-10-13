<?php

namespace App\Http\Requests;

use App\Models\ImeiModel;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyImeiModelRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('imei_model_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:imei_models,id',
        ];
    }
}
