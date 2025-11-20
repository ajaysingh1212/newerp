<?php

namespace App\Http\Requests;

use App\Models\Registration;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyRegistrationRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('registration_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:registrations,id',
        ];
    }
}
