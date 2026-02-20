<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExamResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'term_id' => ['required', 'exists:terms,id'],
            'exam_type' => ['required', Rule::in(['cat', 'midterm', 'endterm'])],
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade' => ['nullable', Rule::in(['A', 'B', 'C', 'D', 'E'])],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
