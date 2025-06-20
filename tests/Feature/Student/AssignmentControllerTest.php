<?php

namespace Tests\Feature\Student;

use App\Models\Assignment;
use App\Models\FlashcardSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $otherStudent;
    protected FlashcardSet $assignedSet;
    protected FlashcardSet $unassignedSet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->student = User::factory()->student()->create();
        $this->otherStudent = User::factory()->student()->create();
        
        $this->assignedSet = FlashcardSet::factory()->create();
        $this->unassignedSet = FlashcardSet::factory()->create();

        Assignment::factory()->create([
            'student_id' => $this->student->id,
            'flashcard_set_id' => $this->assignedSet->id,
        ]);

        $this->actingAs($this->student);
    }

    /** @test */
    public function student_can_view_their_assignments()
    {
        $this->get(route('student.assignments.index'))
            ->assertOk()
            ->assertSee($this->assignedSet->title)
            ->assertDontSee($this->unassignedSet->title);
    }
    
    /** @test */
    public function student_cannot_view_assignments_of_others()
    {
        $otherAssignedSet = FlashcardSet::factory()->create();
        Assignment::factory()->create([
            'student_id' => $this->otherStudent->id,
            'flashcard_set_id' => $otherAssignedSet->id
        ]);

        $this->get(route('student.assignments.index'))
            ->assertSee($this->assignedSet->title)
            ->assertDontSee($otherAssignedSet->title);
    }
}
