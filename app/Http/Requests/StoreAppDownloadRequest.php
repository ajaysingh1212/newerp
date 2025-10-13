<?php

namespace App\Http\Requests;

use App\Models\AppDownload;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreAppDownloadRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('app_download_create');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'required',
            ],
            'user' => [
                'string',
                'required',
                'unique:app_downloads',
            ],
            'password' => [
                'string',
                'required',
                'unique:app_downloads',
            ],
            'appurl' => [
                'string',
                'nullable',
            ],
        ];
    }
}
