<?php

namespace App\Http\Requests;

use App\Models\TimetableSlot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTimetableSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'term_id' => ['required', 'exists:terms,id'],
            'day_of_week' => ['required', 'string', Rule::in([
                'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',
            ])],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $teacherConflict = TimetableSlot::query()
                ->where('teacher_id', $this->teacher_id)
                ->where('term_id', $this->term_id)
                ->where('day_of_week', $this->day_of_week)
                ->where(function ($query): void {
                    $query->where(function ($overlap): void {
                        $overlap->where('start_time', '<', $this->end_time)
                            ->where('end_time', '>', $this->start_time);
                    });
                })->exists();

            if ($teacherConflict) {
                $validator->errors()->add('teacher_id', 'Teacher is already assigned to another class at this time.');
            }

            $classConflict = TimetableSlot::query()
                ->where('school_class_id', $this->school_class_id)
                ->where('term_id', $this->term_id)
                ->where('day_of_week', $this->day_of_week)
                ->where(function ($query): void {
                    $query->where(function ($overlap): void {
                        $overlap->where('start_time', '<', $this->end_time)
                            ->where('end_time', '>', $this->start_time);
                    });
                })->exists();

            if ($classConflict) {
                $validator->errors()->add('school_class_id', 'This class already has a lesson at this time.');
            }
        });
    }
}
