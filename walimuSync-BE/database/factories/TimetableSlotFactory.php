<?php

namespace Database\Factories;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Term;
use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimetableSlot>
 */
class TimetableSlotFactory extends Factory
{
    public function definition(): array
    {
        $startHour = fake()->numberBetween(7, 15);

        return [
            'school_class_id' => SchoolClass::factory(),
            'subject_id' => Subject::factory(),
            'teacher_id' => User::factory(),
            'term_id' => Term::factory(),
            'day_of_week' => fake()->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
            'start_time' => sprintf('%02d:00:00', $startHour),
            'end_time' => sprintf('%02d:00:00', $startHour + 1),
        ];
    }
}
