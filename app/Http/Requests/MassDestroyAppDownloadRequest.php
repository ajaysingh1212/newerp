<?php

namespace App\Http\Requests;

use App\Models\AppDownload;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyAppDownloadRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('app_download_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:app_downloads,id',
        ];
    }
}
