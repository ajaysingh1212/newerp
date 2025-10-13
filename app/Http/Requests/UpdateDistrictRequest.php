<?php

namespace App\Http\Requests;

use App\Models\District;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateDistrictRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('district_edit');
    }

    public function rules()
    {
        return [
            'districts' => [
                'string',
                'required',
            ],
            'country' => [
                'string',
                'required',
            ],
            'select_state_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
