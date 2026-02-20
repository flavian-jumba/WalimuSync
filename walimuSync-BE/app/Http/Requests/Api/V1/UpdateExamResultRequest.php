<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExamResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'student_id' => ['sometimes', 'exists:students,id'],
            'subject_id' => ['sometimes', 'exists:subjects,id'],
            'term_id' => ['sometimes', 'exists:terms,id'],
            'exam_type' => ['sometimes', Rule::in(['cat', 'midterm', 'endterm'])],
            'score' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'grade' => ['nullable', Rule::in(['A', 'B', 'C', 'D', 'E'])],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
