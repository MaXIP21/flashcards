<?php

namespace Tests\Unit\Models;

use App\Models\Assignment;
use App\Models\Flashcard;
use App\Models\FlashcardSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashcardSetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_creator()
    {
        $creator = User::factory()->create();
        $set = FlashcardSet::factory()->create(['created_by' => $creator->id]);

        $this->assertInstanceOf(User::class, $set->creator);
        $this->assertEquals($creator->id, $set->creator->id);
    }

    /** @test */
    public function it_has_many_flashcards()
    {
        $set = FlashcardSet::factory()->create();
        Flashcard::factory()->count(5)->create(['flashcard_set_id' => $set->id]);

        $this->assertCount(5, $set->flashcards);
        $this->assertInstanceOf(Flashcard::class, $set->flashcards->first());
    }

    /** @test */
    public function it_has_many_assignments()
    {
        $set = FlashcardSet::factory()->create();
        Assignment::factory()->count(3)->create(['flashcard_set_id' => $set->id]);

        $this->assertCount(3, $set->assignments);
        $this->assertInstanceOf(Assignment::class, $set->assignments->first());
    }
    
    /** @test */
    public function public_scope_returns_only_public_sets()
    {
        FlashcardSet::factory()->create(['is_public' => true]);
        FlashcardSet::factory()->create(['is_public' => false]);

        $this->assertCount(1, FlashcardSet::public()->get());
    }
    
    /** @test */
    public function created_by_scope_returns_only_sets_created_by_a_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        FlashcardSet::factory()->create(['created_by' => $user1->id]);
        FlashcardSet::factory()->create(['created_by' => $user2->id]);

        $this->assertCount(1, FlashcardSet::createdBy($user1->id)->get());
        $this->assertCount(1, FlashcardSet::createdBy($user2->id)->get());
    }

    /** @test */
    public function is_assigned_to_by_method_works_correctly()
    {
        $teacher = User::factory()->teacher()->create();
        $student = User::factory()->student()->create();
        $set = FlashcardSet::factory()->create();

        Assignment::factory()->create([
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'flashcard_set_id' => $set->id,
        ]);

        $this->assertTrue($set->isAssignedToBy($student, $teacher));
    }

    /** @test */
    public function unique_identifier_is_generated_on_creation()
    {
        $set = FlashcardSet::factory()->create(['unique_identifier' => null]);
        $this->assertNotNull($set->unique_identifier);
    }
}
