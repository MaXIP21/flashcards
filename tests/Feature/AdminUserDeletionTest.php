<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_regular_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_can_delete_teacher(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $teacher));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $teacher->id]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_delete_last_admin(): void
    {
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);

        // Delete first admin
        $this->actingAs($admin2)
            ->delete(route('admin.users.destroy', $admin1));

        // Try to delete the last admin
        $response = $this->actingAs($admin2)
            ->delete(route('admin.users.destroy', $admin2));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin2->id]);
    }

    public function test_teacher_cannot_delete_users(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'is_activated' => true]);
        $user = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($teacher)
            ->delete(route('admin.users.destroy', $user));

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_student_cannot_delete_users(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $user = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($student)
            ->delete(route('admin.users.destroy', $user));

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_guest_cannot_delete_users(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_delete_user_cascades_related_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);

        // Create flashcard set for the teacher
        $flashcardSet = $teacher->flashcardSets()->create([
            'title' => 'Test Set',
            'description' => 'Test Description',
            'source_language' => 'en',
            'target_language' => 'es',
            'is_public' => false,
            'unique_identifier' => 'test-set-123'
        ]);

        // Create flashcards for the set
        $flashcard = $flashcardSet->flashcards()->create([
            'source_word' => 'Hello',
            'target_word' => 'Hola',
            'position' => 1
        ]);

        // Delete the teacher
        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $teacher));

        // Check that related data is also deleted
        $this->assertDatabaseMissing('users', ['id' => $teacher->id]);
        $this->assertDatabaseMissing('flashcard_sets', ['id' => $flashcardSet->id]);
        $this->assertDatabaseMissing('flashcards', ['id' => $flashcard->id]);
    }
}
