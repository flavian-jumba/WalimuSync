<?php

namespace Database\Factories;

use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolClass>
 */
class SchoolClassFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Form '.fake()->numberBetween(1, 4),
            'stream' => fake()->randomElement(['East', 'West', 'North', 'South']),
            'academic_year' => fake()->year(),
        ];
    }
}
