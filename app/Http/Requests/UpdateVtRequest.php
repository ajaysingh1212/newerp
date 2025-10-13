<?php

namespace App\Http\Requests;

use App\Models\Vt;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateVtRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('vt_edit');
    }

    public function rules()
    {
        return [
            'vts_number' => [
                'string',
                'required',
                'unique:vts,vts_number,' . request()->route('vt')->id,
            ],
            'sim_number' => [
                'string',
                'required',
                'unique:vts,sim_number,' . request()->route('vt')->id,
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
