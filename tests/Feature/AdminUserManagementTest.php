<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($admin);

        $response = $this->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('User Management');
        $response->assertSee($teacher->name);
        $response->assertSee($student->name);
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'is_activated' => true]);
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($teacher);
        $response = $this->get('/admin/users');
        $response->assertStatus(403);

        $this->actingAs($student);
        $response = $this->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_admin_can_view_user_details(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $this->actingAs($admin);

        $response = $this->get("/admin/users/{$teacher->id}");
        $response->assertStatus(200);
        $response->assertSee($teacher->name);
        $response->assertSee($teacher->email);
        $response->assertSee('Pending Activation');
    }

    public function test_admin_cannot_view_nonexistent_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        $response = $this->get('/admin/users/999');
        $response->assertStatus(404);
    }

    public function test_user_list_shows_activation_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $activatedTeacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => true,
        ]);
        $nonActivatedTeacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Active');
        $response->assertSee('Pending Activation');
    }

    public function test_user_list_filters_by_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($admin);

        // Filter by teacher role
        $response = $this->get('/admin/users?role=teacher');
        $response->assertStatus(200);
        $response->assertSee($teacher->name);
        $response->assertDontSee($student->name);

        // Filter by student role
        $response = $this->get('/admin/users?role=student');
        $response->assertStatus(200);
        $response->assertSee($student->name);
        $response->assertDontSee($teacher->name);
    }

    public function test_user_list_filters_by_activation_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $activatedTeacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => true,
        ]);
        $nonActivatedTeacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $this->actingAs($admin);

        // Filter by active status
        $response = $this->get('/admin/users?status=active');
        $response->assertStatus(200);
        $response->assertSee($activatedTeacher->name);
        $response->assertDontSee($nonActivatedTeacher->name);

        // Filter by inactive status
        $response = $this->get('/admin/users?status=inactive');
        $response->assertStatus(200);
        $response->assertSee($nonActivatedTeacher->name);
        $response->assertDontSee($activatedTeacher->name);
    }

    public function test_admin_can_activate_teacher(): void
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

    public function test_admin_can_deactivate_teacher(): void
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

    public function test_admin_cannot_activate_non_teacher(): void
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

    public function test_admin_cannot_deactivate_non_teacher(): void
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

    public function test_admin_cannot_activate_already_activated_teacher(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->post("/admin/users/{$teacher->id}/activate");
        $response->assertRedirect();

        $teacher->refresh();
        $this->assertTrue($teacher->is_activated);
    }

    public function test_admin_cannot_deactivate_already_deactivated_teacher(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $this->actingAs($admin);

        $response = $this->post("/admin/users/{$teacher->id}/deactivate");
        $response->assertRedirect();

        $teacher->refresh();
        $this->assertFalse($teacher->is_activated);
    }

    public function test_activation_action_updates_user_details_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $this->actingAs($admin);

        // Activate the teacher
        $this->post("/admin/users/{$teacher->id}/activate");

        // Check the user details page shows updated status
        $response = $this->get("/admin/users/{$teacher->id}");
        $response->assertStatus(200);
        $response->assertSee('Active');
        $response->assertDontSee('Pending Activation');
    }

    public function test_user_management_shows_activation_history(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => true,
            'activated_at' => now(),
            'activated_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        $response = $this->get("/admin/users/{$teacher->id}");
        $response->assertStatus(200);
        $response->assertSee('Activated by: ' . $admin->name);
        $response->assertSee($teacher->activated_at->format('F j, Y, g:i a'));
    }
}
