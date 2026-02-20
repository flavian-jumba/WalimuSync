<?php

namespace Database\Factories;

use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Substitution>
 */
class SubstitutionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'timetable_slot_id' => TimetableSlot::factory(),
            'substitute_teacher_id' => User::factory(),
            'date' => fake()->dateTimeBetween('now', '+14 days'),
        ];
    }
}
