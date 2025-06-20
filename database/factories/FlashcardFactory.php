<?php

namespace Database\Factories;

use App\Models\FlashcardSet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flashcard>
 */
class FlashcardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $words = [
            ['hello', 'hola'],
            ['goodbye', 'adiós'],
            ['thank you', 'gracias'],
            ['please', 'por favor'],
            ['yes', 'sí'],
            ['no', 'no'],
            ['water', 'agua'],
            ['food', 'comida'],
            ['house', 'casa'],
            ['car', 'coche'],
            ['book', 'libro'],
            ['time', 'tiempo'],
            ['day', 'día'],
            ['night', 'noche'],
            ['friend', 'amigo'],
            ['family', 'familia'],
            ['work', 'trabajo'],
            ['school', 'escuela'],
            ['city', 'ciudad'],
            ['country', 'país'],
        ];

        $wordPair = $this->faker->randomElement($words);

        return [
            'flashcard_set_id' => FlashcardSet::factory(),
            'source_word' => $wordPair[0],
            'target_word' => $wordPair[1],
            'position' => $this->faker->numberBetween(1, 100),
        ];
    }
}
