<?php

namespace Database\Factories;

use App\Models\AcademicCalendar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicCalendar>
 */
class AcademicCalendarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'date' => fake()->date(),
            'end_date' => null,
            'type' => fake()->randomElement(['holiday', 'exam', 'event', 'meeting', 'break', 'closure', 'other']),
            'is_all_day' => true,
            'start_time' => null,
            'end_time' => null,
            'description' => fake()->optional()->sentence(),
            'suppresses_notifications' => false,
        ];
    }

    public function holiday(): static
    {
        return $this->state([
            'type' => 'holiday',
            'is_all_day' => true,
            'suppresses_notifications' => true,
        ]);
    }

    public function schoolBreak(): static
    {
        return $this->state([
            'type' => 'break',
            'is_all_day' => true,
            'suppresses_notifications' => true,
        ]);
    }

    public function closure(): static
    {
        return $this->state([
            'type' => 'closure',
            'is_all_day' => true,
            'suppresses_notifications' => true,
        ]);
    }

    public function meeting(): static
    {
        return $this->state([
            'type' => 'meeting',
            'is_all_day' => false,
            'suppresses_notifications' => true,
        ]);
    }

    public function suppressing(): static
    {
        return $this->state([
            'suppresses_notifications' => true,
        ]);
    }
}
