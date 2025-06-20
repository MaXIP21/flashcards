<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Flashcard;
use App\Models\FlashcardSet;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PracticeTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected FlashcardSet $assignedSet;
    protected FlashcardSet $privateSet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->student = User::factory()->student()->create();
        $this->assignedSet = FlashcardSet::factory()->create();
        $this->privateSet = FlashcardSet::factory()->create(['is_public' => false]);

        Flashcard::factory()->count(10)->create(['flashcard_set_id' => $this->assignedSet->id]);
        Flashcard::factory()->count(10)->create(['flashcard_set_id' => $this->privateSet->id]);

        Assignment::factory()->create([
            'student_id' => $this->student->id,
            'flashcard_set_id' => $this->assignedSet->id,
        ]);
        
        $this->actingAs($this->student);
    }

    /** @test */
    public function student_can_start_practice_session_for_assigned_set()
    {
        $this->get(route('practice.start', $this->assignedSet))
            ->assertOk()
            ->assertViewIs('practice.index');

        $this->assertDatabaseHas('user_progress', [
            'user_id' => $this->student->id,
            'flashcard_set_id' => $this->assignedSet->id,
        ]);
    }

    /** @test */
    public function student_can_get_their_progress()
    {
        UserProgress::factory()->create([
            'user_id' => $this->student->id,
            'flashcard_set_id' => $this->assignedSet->id,
            'current_position' => 5
        ]);

        $this->getJson(route('practice.get-progress', $this->assignedSet))
            ->assertOk()
            ->assertJson([
                'current_position' => 5,
            ]);
    }

    /** @test */
    public function student_can_save_their_progress()
    {
        $this->postJson(route('practice.save-progress', $this->assignedSet), [
            'current_position' => 7,
            'completed' => false,
        ])->assertOk();

        $this->assertDatabaseHas('user_progress', [
            'user_id' => $this->student->id,
            'flashcard_set_id' => $this->assignedSet->id,
            'current_position' => 7,
            'completed' => false,
        ]);
    }

    /** @test */
    public function student_can_restart_their_progress()
    {
        UserProgress::factory()->create([
            'user_id' => $this->student->id,
            'flashcard_set_id' => $this->assignedSet->id,
            'current_position' => 10,
            'completed' => true
        ]);
        
        $this->post(route('practice.restart', $this->assignedSet))
            ->assertRedirect(route('practice.index', $this->assignedSet));

        $this->assertDatabaseHas('user_progress', [
            'user_id' => $this->student->id,
            'flashcard_set_id' => $this->assignedSet->id,
            'current_position' => 0,
            'completed' => false,
        ]);
    }
    
    /** @test */
    public function student_can_exit_session()
    {
        $this->withSession(['practice_set_id' => $this->assignedSet->id]);
        
        $this->post(route('practice.exit', $this->assignedSet))
            ->assertRedirect(route('dashboard'))
            ->assertSessionMissing('practice_set_id');
    }

    /** @test */
    public function student_cannot_practice_unassigned_private_set()
    {
        $this->get(route('practice.start', $this->privateSet))
            ->assertForbidden();
    }
}
