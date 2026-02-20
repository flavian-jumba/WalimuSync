<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubstitutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'timetable_slot_id' => ['required', 'exists:timetable_slots,id'],
            'substitute_teacher_id' => ['required', 'exists:users,id'],
            'date' => ['required', 'date'],
        ];
    }
}
