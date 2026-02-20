<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamResult>
 */
class ExamResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $score = fake()->randomFloat(2, 0, 100);

        return [
            'student_id' => Student::factory(),
            'subject_id' => Subject::factory(),
            'term_id' => Term::factory(),
            'exam_type' => fake()->randomElement(['cat', 'midterm', 'endterm']),
            'score' => $score,
            'grade' => $this->calculateGrade($score),
            'remarks' => fake()->optional()->sentence(),
            'recorded_by' => User::factory(),
        ];
    }

    private function calculateGrade(float $score): string
    {
        return match (true) {
            $score >= 80 => 'A',
            $score >= 75 => 'A-',
            $score >= 70 => 'B+',
            $score >= 65 => 'B',
            $score >= 60 => 'B-',
            $score >= 55 => 'C+',
            $score >= 50 => 'C',
            $score >= 45 => 'C-',
            $score >= 40 => 'D+',
            $score >= 35 => 'D',
            $score >= 30 => 'D-',
            default => 'E',
        };
    }
}
