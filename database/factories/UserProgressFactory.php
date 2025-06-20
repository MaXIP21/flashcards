<?php

namespace Database\Factories;

use App\Models\FlashcardSet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProgress>
 */
class UserProgressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'flashcard_set_id' => FlashcardSet::factory(),
            'current_position' => 0,
            'completed' => false,
            'last_accessed' => now(),
        ];
    }
}
