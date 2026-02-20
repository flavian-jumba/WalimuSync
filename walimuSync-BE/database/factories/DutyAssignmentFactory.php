<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DutyAssignment>
 */
class DutyAssignmentFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('now', '+30 days');

        return [
            'teacher_id' => User::factory(),
            'start_date' => $start,
            'end_date' => fake()->dateTimeBetween($start, '+60 days'),
        ];
    }
}
