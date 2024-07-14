<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreBookingRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('booking_create');
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
            'start_book' => [
                'required',
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
            ],
            'finish_book' => [
                'required',
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
            ],
        ];
    }
}
