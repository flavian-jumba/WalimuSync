<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFeeCollectionRequest extends FormRequest
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
            'description' => ['nullable', 'string'],
            'type' => ['sometimes', Rule::in(['remedial', 'lunch', 'exam', 'trip', 'uniform', 'other'])],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'term_id' => ['sometimes', 'exists:terms,id'],
            'assigned_teacher_id' => ['nullable', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
            'status' => ['sometimes', Rule::in(['open', 'closed'])],
        ];
    }
}
