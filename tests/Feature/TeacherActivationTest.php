<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_registration_creates_non_activated_account(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Teacher',
            'email' => 'teacher@example.com',
            'role' => 'teacher',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/activation-pending');
        
        $teacher = User::where('email', 'teacher@example.com')->first();
        $this->assertNotNull($teacher);
        $this->assertEquals('teacher', $teacher->role);
        $this->assertFalse($teacher->is_activated);
        $this->assertNull($teacher->activated_at);
        $this->assertNull($teacher->activated_by);
    }

    public function test_student_registration_creates_activated_account(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Student',
            'email' => 'student@example.com',
            'role' => 'student',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        
        $student = User::where('email', 'student@example.com')->first();
        $this->assertNotNull($student);
        $this->assertEquals('student', $student->role);
        $this->assertTrue($student->is_activated);
    }

    public function test_non_activated_teacher_cannot_access_teacher_routes(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $this->actingAs($teacher);

        // Try to access teacher routes
        $response = $this->get('/teacher/flashcard-sets');
        $response->assertRedirect('/activation-pending');

        $response = $this->get('/teacher/assignments');
        $response->assertRedirect('/activation-pending');
    }

    public function test_activated_teacher_can_access_teacher_routes(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => true,
        ]);

        $this->actingAs($teacher);

        // Should be able to access teacher routes
        $response = $this->get('/teacher/flashcard-sets');
        $response->assertStatus(200);
    }

    public function test_admin_can_view_user_management_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('User Management');
    }

    public function test_non_admin_cannot_view_user_management_page(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'is_activated' => true]);
        $this->actingAs($teacher);

        $response = $this->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_admin_can_activate_teacher_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $this->actingAs($admin);

        $response = $this->post("/admin/users/{$teacher->id}/activate");
        $response->assertRedirect();

        $teacher->refresh();
        $this->assertTrue($teacher->is_activated);
        $this->assertNotNull($teacher->activated_at);
        $this->assertEquals($admin->id, $teacher->activated_by);
    }

    public function test_admin_can_deactivate_teacher_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => true,
            'activated_at' => now(),
            'activated_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        $response = $this->post("/admin/users/{$teacher->id}/deactivate");
        $response->assertRedirect();

        $teacher->refresh();
        $this->assertFalse($teacher->is_activated);
        $this->assertNull($teacher->activated_at);
        $this->assertNull($teacher->activated_by);
    }

    public function test_admin_cannot_activate_non_teacher_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create([
            'role' => 'student',
            'is_activated' => false,
        ]);

        $this->actingAs($admin);

        $response = $this->post("/admin/users/{$student->id}/activate");
        $response->assertRedirect();

        $student->refresh();
        $this->assertFalse($student->is_activated);
    }

    public function test_admin_cannot_deactivate_non_teacher_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create([
            'role' => 'student',
            'is_activated' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->post("/admin/users/{$student->id}/deactivate");
        $response->assertRedirect();

        $student->refresh();
        $this->assertTrue($student->is_activated);
    }

    public function test_user_management_page_shows_activation_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Pending Activation');
        $response->assertSee('Activate');
    }

    public function test_user_management_page_shows_activated_teachers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Active');
        $response->assertSee('Deactivate');
    }

    public function test_user_management_filters_work(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);
        $student = User::factory()->create([
            'role' => 'student',
            'is_activated' => true,
        ]);

        $this->actingAs($admin);

        // Filter by teacher role
        $response = $this->get('/admin/users?role=teacher');
        $response->assertStatus(200);
        $response->assertSee($teacher->name);
        $response->assertDontSee($student->name);

        // Filter by inactive status
        $response = $this->get('/admin/users?status=inactive');
        $response->assertStatus(200);
        $response->assertSee($teacher->name);
        $response->assertDontSee($student->name);
    }

    public function test_activation_pending_page_is_accessible_to_non_activated_teachers(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $this->actingAs($teacher);

        $response = $this->get('/activation-pending');
        $response->assertStatus(200);
        $response->assertSee('Account Activation Required');
    }

    public function test_activated_teachers_are_redirected_from_activation_pending_page(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => true,
        ]);

        $this->actingAs($teacher);

        $response = $this->get('/activation-pending');
        $response->assertRedirect('/dashboard');
    }

    public function test_user_model_activation_methods_work(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        // Test activation
        $teacher->activate($admin);
        $this->assertTrue($teacher->is_activated);
        $this->assertNotNull($teacher->activated_at);
        $this->assertEquals($admin->id, $teacher->activated_by);

        // Test deactivation
        $teacher->deactivate();
        $this->assertFalse($teacher->is_activated);
        $this->assertNull($teacher->activated_at);
        $this->assertNull($teacher->activated_by);
    }

    public function test_user_model_relationships_work(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => true,
            'activated_by' => $admin->id,
        ]);

        // Test activatedBy relationship
        $this->assertEquals($admin->id, $teacher->activatedBy->id);

        // Test activatedUsers relationship
        $this->assertTrue($admin->activatedUsers->contains($teacher));
    }
}
