<?php

namespace Tests\Unit\Models;

use App\Models\Assignment;
use App\Models\FlashcardSet;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_have_different_roles()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isTeacher());

        $this->assertTrue($teacher->isTeacher());
        $this->assertFalse($teacher->isAdmin());

        $this->assertTrue($student->isStudent());
        $this->assertFalse($student->isTeacher());
    }

    /** @test */
    public function has_role_methods_work_correctly()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertFalse($admin->hasRole('teacher'));

        $this->assertTrue($teacher->hasAnyRole(['admin', 'teacher']));
        $this->assertFalse($teacher->hasAnyRole(['admin', 'student']));
    }

    /** @test */
    public function user_has_many_flashcard_sets()
    {
        $user = User::factory()->create();
        FlashcardSet::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(FlashcardSet::class, $user->flashcardSets->first());
        $this->assertCount(1, $user->flashcardSets);
    }

    /** @test */
    public function user_has_many_assignments_as_teacher()
    {
        $teacher = User::factory()->teacher()->create();
        Assignment::factory()->create(['teacher_id' => $teacher->id]);

        $this->assertInstanceOf(Assignment::class, $teacher->teacherAssignments->first());
        $this->assertCount(1, $teacher->teacherAssignments);
    }
    
    /** @test */
    public function user_has_many_assignments_as_student()
    {
        $student = User::factory()->student()->create();
        Assignment::factory()->create(['student_id' => $student->id]);

        $this->assertInstanceOf(Assignment::class, $student->studentAssignments->first());
        $this->assertCount(1, $student->studentAssignments);
    }

    /** @test */
    public function user_has_many_progress_records()
    {
        $user = User::factory()->create();
        UserProgress::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(UserProgress::class, $user->progress->first());
        $this->assertCount(1, $user->progress);
    }
}
