<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademicCalendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'date' => ['sometimes', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:date'],
            'type' => ['sometimes', Rule::in(['holiday', 'exam', 'event', 'meeting', 'break', 'closure', 'other'])],
            'is_all_day' => ['boolean'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'description' => ['nullable', 'string'],
            'suppresses_notifications' => ['boolean'],
        ];
    }
}
