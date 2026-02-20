<?php

namespace Database\Factories;

use App\Models\FeeCollection;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeePayment>
 */
class FeePaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fee_collection_id' => FeeCollection::factory(),
            'student_id' => Student::factory(),
            'amount_paid' => fake()->randomFloat(2, 50, 5000),
            'collected_by' => User::factory(),
            'payment_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'receipt_number' => fake()->optional()->numerify('RCP-######'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
