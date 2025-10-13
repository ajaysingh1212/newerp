<?php

namespace App\Http\Requests;

use App\Models\AppLink;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyAppLinkRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('app_link_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:app_links,id',
        ];
    }
}
