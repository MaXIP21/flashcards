<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherActivationMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_middleware_allows_activated_teachers(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => true,
        ]);

        $this->actingAs($teacher);

        $response = $this->get('/teacher/flashcard-sets');
        $response->assertStatus(200);
    }

    public function test_middleware_redirects_non_activated_teachers(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $this->actingAs($teacher);

        $response = $this->get('/teacher/flashcard-sets');
        $response->assertRedirect('/activation-pending');
    }

    public function test_middleware_allows_students(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_activated' => false,
        ]);

        $this->actingAs($student);

        $response = $this->get('/student/assignments');
        $response->assertStatus(200);
    }

    public function test_middleware_allows_admins(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/users');
        $response->assertStatus(200);
    }

    public function test_middleware_redirects_on_teacher_routes(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $this->actingAs($teacher);

        // Test various teacher routes that exist
        $routes = [
            '/teacher/flashcard-sets',
            '/teacher/assignments',
            '/teacher/progress',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/activation-pending');
        }
    }

    public function test_middleware_does_not_redirect_on_non_teacher_routes(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $this->actingAs($teacher);

        // These routes should not be affected by teacher activation middleware
        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        $response = $this->get('/profile');
        $response->assertStatus(200);
    }

    public function test_middleware_works_with_guest_users(): void
    {
        // Guest users should not be affected by teacher activation middleware
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_middleware_preserves_intended_url(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $this->actingAs($teacher);

        $response = $this->get('/teacher/flashcard-sets');
        $response->assertRedirect('/activation-pending');

        // After activation, should be able to access the intended URL
        $teacher->update(['is_activated' => true]);

        $response = $this->get('/teacher/flashcard-sets');
        $response->assertStatus(200);
    }
}
