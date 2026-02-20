<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAnnouncementRequest extends FormRequest
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
            'body' => ['sometimes', 'string'],
            'audience' => ['sometimes', Rule::in(['all', 'teachers', 'class'])],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'published_at' => ['nullable', 'date'],
            'is_pinned' => ['boolean'],
        ];
    }
}
