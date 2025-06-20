<?php

namespace Database\Factories;

use App\Models\FlashcardSet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'teacher_id' => User::factory()->teacher(),
            'student_id' => User::factory()->student(),
            'flashcard_set_id' => FlashcardSet::factory(),
            'assigned_at' => now(),
            'due_date' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
        ];
    }
}
