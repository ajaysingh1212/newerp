<?php

namespace App\Http\Requests;

use App\Models\AppDownload;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateAppDownloadRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('app_download_edit');
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
                'unique:app_downloads,user,' . request()->route('app_download')->id,
            ],
            'password' => [
                'string',
                'required',
                'unique:app_downloads,password,' . request()->route('app_download')->id,
            ],
            'appurl' => [
                'string',
                'nullable',
            ],
        ];
    }
}
