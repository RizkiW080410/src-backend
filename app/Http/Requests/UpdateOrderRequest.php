<?php

namespace App\Http\Requests;

use App\Models\Order;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateOrderRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('order_edit');
    }

    public function rules()
    {
        return [
            'fullname' => [
                'string',
                'required',
            ],
            'phone' => [
                'string',
                'required',
            ],
            'email' => [
                'required',
                'email',
            ],
            'products' => [
                'required',
                'array',
            ],
            'products.*' => [
                'integer',
            ],
            'quantities' => [
                'required',
                'array',
            ],
            'quantities.*' => [
                'integer',
                'min:1',
            ],
        ];
    }
}