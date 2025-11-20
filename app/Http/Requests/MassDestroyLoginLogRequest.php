<?php

namespace App\Http\Requests;

use App\Models\LoginLog;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyLoginLogRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('login_log_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:login_logs,id',
        ];
    }
}
