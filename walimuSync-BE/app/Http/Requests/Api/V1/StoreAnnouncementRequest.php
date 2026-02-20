<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnnouncementRequest extends FormRequest
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
            'body' => ['required', 'string'],
            'audience' => ['required', Rule::in(['all', 'teachers', 'class'])],
            'school_class_id' => ['nullable', 'required_if:audience,class', 'exists:school_classes,id'],
            'published_at' => ['nullable', 'date'],
            'is_pinned' => ['boolean'],
        ];
    }
}
