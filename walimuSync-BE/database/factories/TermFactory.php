<?php

namespace Database\Factories;

use App\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Term>
 */
class TermFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+3 months');

        return [
            'name' => 'Term '.fake()->numberBetween(1, 3).' '.fake()->year(),
            'start_date' => $startDate,
            'end_date' => fake()->dateTimeBetween($startDate, '+4 months'),
            'is_active' => false,
        ];
    }

    public function active(): static
    {
        return $this->state(['is_active' => true]);
    }
}
