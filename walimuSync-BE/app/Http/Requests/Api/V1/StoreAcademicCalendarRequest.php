<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAcademicCalendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:date'],
            'type' => ['required', Rule::in(['holiday', 'exam', 'event', 'meeting', 'break', 'closure', 'other'])],
            'is_all_day' => ['boolean'],
            'start_time' => ['nullable', 'date_format:H:i', 'required_if:is_all_day,false'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time', 'required_if:is_all_day,false'],
            'description' => ['nullable', 'string'],
            'suppresses_notifications' => ['boolean'],
        ];
    }
}
