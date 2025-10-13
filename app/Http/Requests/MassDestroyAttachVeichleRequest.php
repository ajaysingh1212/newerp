<?php

namespace App\Http\Requests;

use App\Models\AttachVeichle;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyAttachVeichleRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('attach_veichle_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:attach_veichles,id',
        ];
    }
}
