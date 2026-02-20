<?php

namespace App\Http\Requests\Api\V1;

use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkStoreExamResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'term_id' => ['required', 'exists:terms,id'],
            'exam_type' => ['required', Rule::in(['cat', 'midterm', 'endterm'])],
            'results' => ['required', 'array', 'min:1'],
            'results.*.student_id' => ['required', 'exists:students,id'],
            'results.*.score' => ['required', 'numeric', 'min:0', 'max:100'],
            'results.*.grade' => ['nullable', Rule::in(['A', 'B', 'C', 'D', 'E'])],
            'results.*.remarks' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $classId = $this->school_class_id;
            $classTeacherId = SchoolClass::find($classId)?->teacher_id;

            // Verify the authenticated user is the class teacher
            if ($classTeacherId && $classTeacherId !== $this->user()->id) {
                $validator->errors()->add('school_class_id', 'You are not the class teacher for this class.');
            }

            // Verify all students belong to this class
            $studentIds = collect($this->results)->pluck('student_id');
            $invalidStudents = Student::query()
                ->whereIn('id', $studentIds)
                ->where('school_class_id', '!=', $classId)
                ->exists();

            if ($invalidStudents) {
                $validator->errors()->add('results', 'Some students do not belong to the selected class.');
            }
        });
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'results.*.student_id.required' => 'Each result must include a student.',
            'results.*.student_id.exists' => 'Student #:position does not exist.',
            'results.*.score.required' => 'Each result must include a score.',
            'results.*.score.min' => 'Score must be at least 0.',
            'results.*.score.max' => 'Score must not exceed 100.',
        ];
    }
}
