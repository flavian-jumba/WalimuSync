<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Mathematics', 'English', 'Kiswahili', 'Physics', 'Chemistry',
                'Biology', 'History', 'Geography', 'CRE', 'Business Studies',
                'Computer Studies', 'Agriculture', 'French', 'Music', 'Art',
            ]),
            'code' => fake()->unique()->bothify('??###'),
        ];
    }
}
