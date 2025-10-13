<?php

namespace App\Http\Requests;

use App\Models\AttachVeichle;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateAttachVeichleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('attach_veichle_edit');
    }

    public function rules()
    {
        return [
            'select_user_id' => [
                'required',
                'integer',
            ],
            'vehicles.*' => [
                'integer',
            ],
            'vehicles' => [
                'array',
            ],
        ];
    }
}
