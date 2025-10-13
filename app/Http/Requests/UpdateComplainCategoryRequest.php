<?php

namespace App\Http\Requests;

use App\Models\ComplainCategory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateComplainCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('complain_category_edit');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'required',
            ],
        ];
    }
}
