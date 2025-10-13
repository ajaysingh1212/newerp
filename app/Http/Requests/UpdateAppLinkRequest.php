<?php

namespace App\Http\Requests;

use App\Models\AppLink;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateAppLinkRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('app_link_edit');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'required',
            ],
            'link' => [
                'string',
                'required',
                'unique:app_links,link,' . request()->route('app_link')->id,
            ],
        ];
    }
}
