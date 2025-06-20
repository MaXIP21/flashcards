<?php

namespace Tests\Unit\Models;

use App\Models\Flashcard;
use App\Models\FlashcardSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashcardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_flashcard_set()
    {
        $set = FlashcardSet::factory()->create();
        $flashcard = Flashcard::factory()->create(['flashcard_set_id' => $set->id]);

        $this->assertInstanceOf(FlashcardSet::class, $flashcard->flashcardSet);
        $this->assertEquals($set->id, $flashcard->flashcardSet->id);
    }

    /** @test */
    public function ordered_scope_returns_flashcards_by_position()
    {
        $set = FlashcardSet::factory()->create();
        $card1 = Flashcard::factory()->create(['flashcard_set_id' => $set->id, 'position' => 2]);
        $card2 = Flashcard::factory()->create(['flashcard_set_id' => $set->id, 'position' => 1]);
        $card3 = Flashcard::factory()->create(['flashcard_set_id' => $set->id, 'position' => 3]);

        $orderedCards = Flashcard::ordered()->get();

        $this->assertEquals($card2->id, $orderedCards[0]->id);
        $this->assertEquals($card1->id, $orderedCards[1]->id);
        $this->assertEquals($card3->id, $orderedCards[2]->id);
    }
}
