<?php

namespace App\Http\Requests;

use App\Models\ActivationRequest;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyActivationRequestRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('activation_request_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:activation_requests,id',
        ];
    }
}
