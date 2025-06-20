<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FlashcardSet>
 */
class FlashcardSetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $languages = [
            ['English', 'Spanish'],
            ['English', 'French'],
            ['English', 'German'],
            ['Spanish', 'English'],
            ['French', 'English'],
            ['German', 'English'],
        ];

        $languagePair = $this->faker->randomElement($languages);

        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'source_language' => $languagePair[0],
            'target_language' => $languagePair[1],
            'is_public' => $this->faker->boolean(20), // 20% chance of being public
            'unique_identifier' => $this->faker->unique()->regexify('[A-Za-z0-9]{10}'),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the flashcard set is public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }

    /**
     * Indicate that the flashcard set is private.
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => false,
        ]);
    }
}
