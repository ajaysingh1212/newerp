<?php

namespace App\Http\Requests;

use App\Models\Vt;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreVtRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('vt_create');
    }

    public function rules()
    {
        return [
            'vts_number' => [
                'string',
                'required',
                'unique:vts',
            ],
            'sim_number' => [
                'string',
                'required',
                'unique:vts',
            ],
            'operator' => [
                'string',
                'required',
            ],
            'product_status' => [
                'required',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
