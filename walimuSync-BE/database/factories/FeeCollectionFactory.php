<?php

namespace Database\Factories;

use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeeCollection>
 */
class FeeCollectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'type' => fake()->randomElement(['remedial', 'lunch', 'exam', 'trip', 'uniform', 'other']),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'school_class_id' => null,
            'term_id' => Term::factory(),
            'assigned_teacher_id' => User::factory(),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'status' => 'open',
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'closed',
        ]);
    }
}
