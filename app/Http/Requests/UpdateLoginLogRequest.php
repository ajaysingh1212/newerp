<?php

namespace App\Http\Requests;

use App\Models\LoginLog;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateLoginLogRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('login_log_edit');
    }

    public function rules()
    {
        return [
            'use_id' => [
                'required',
                'integer',
            ],
            'ip_address' => [
                'string',
                'nullable',
            ],
            'device' => [
                'string',
                'nullable',
            ],
            'location' => [
                'string',
                'nullable',
            ],
            'logged_in_at' => [
                'string',
                'nullable',
            ],
        ];
    }
}
