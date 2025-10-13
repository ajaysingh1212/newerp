<?php

namespace App\Http\Requests;

use App\Models\CheckComplain;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCheckComplainRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('check_complain_create');
    }

    public function rules()
    {
        return [

    



            'reason' => [
                'required',
            ],
    
            'attechment' => [
                'array',
            ],
        ];
    }
}
