<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_activate_method_sets_correct_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $teacher->activate($admin);

        $this->assertTrue($teacher->is_activated);
        $this->assertNotNull($teacher->activated_at);
        $this->assertEquals($admin->id, $teacher->activated_by);
    }

    public function test_deactivate_method_clears_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => true,
            'activated_at' => now(),
            'activated_by' => $admin->id,
        ]);

        $teacher->deactivate();

        $this->assertFalse($teacher->is_activated);
        $this->assertNull($teacher->activated_at);
        $this->assertNull($teacher->activated_by);
    }

    public function test_activated_by_relationship_works(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => true,
            'activated_by' => $admin->id,
        ]);

        $this->assertEquals($admin->id, $teacher->activatedBy->id);
        $this->assertEquals($admin->name, $teacher->activatedBy->name);
    }

    public function test_activated_users_relationship_works(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher1 = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => true,
            'activated_by' => $admin->id,
        ]);
        $teacher2 = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => true,
            'activated_by' => $admin->id,
        ]);

        $this->assertCount(2, $admin->activatedUsers);
        $this->assertTrue($admin->activatedUsers->contains($teacher1));
        $this->assertTrue($admin->activatedUsers->contains($teacher2));
    }

    public function test_activated_by_relationship_returns_null_when_not_activated(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
            'activated_by' => null,
        ]);

        $this->assertNull($teacher->activatedBy);
    }

    public function test_activated_users_relationship_returns_empty_collection_when_no_activations(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertCount(0, $admin->activatedUsers);
    }

    public function test_activation_timestamp_is_set_correctly(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $beforeActivation = now()->subSecond();
        $teacher->activate($admin);
        $afterActivation = now()->addSecond();

        $this->assertTrue($teacher->activated_at->between($beforeActivation, $afterActivation));
    }

    public function test_multiple_activations_update_timestamp(): void
    {
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $teacher->activate($admin1);
        $firstActivationTime = $teacher->activated_at;
        $firstActivatedBy = $teacher->activated_by;

        // Deactivate and reactivate
        $teacher->deactivate();
        sleep(1); // Ensure different timestamp
        $teacher->activate($admin2);

        $this->assertNotEquals($firstActivationTime, $teacher->activated_at);
        $this->assertNotEquals($firstActivatedBy, $teacher->activated_by);
        $this->assertEquals($admin2->id, $teacher->activated_by);
    }

    public function test_activation_does_not_affect_other_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $originalName = $teacher->name;
        $originalEmail = $teacher->email;
        $originalRole = $teacher->role;

        $teacher->activate($admin);

        $this->assertEquals($originalName, $teacher->name);
        $this->assertEquals($originalEmail, $teacher->email);
        $this->assertEquals($originalRole, $teacher->role);
    }

    public function test_deactivation_does_not_affect_other_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => true,
            'activated_at' => now(),
            'activated_by' => $admin->id,
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $originalName = $teacher->name;
        $originalEmail = $teacher->email;
        $originalRole = $teacher->role;

        $teacher->deactivate();

        $this->assertEquals($originalName, $teacher->name);
        $this->assertEquals($originalEmail, $teacher->email);
        $this->assertEquals($originalRole, $teacher->role);
    }

    public function test_activation_works_with_different_admin_users(): void
    {
        $admin1 = User::factory()->create(['role' => 'admin', 'name' => 'Admin 1']);
        $admin2 = User::factory()->create(['role' => 'admin', 'name' => 'Admin 2']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_activated' => false,
        ]);

        $teacher->activate($admin1);
        $this->assertEquals($admin1->id, $teacher->activated_by);
        $this->assertEquals('Admin 1', $teacher->activatedBy->name);

        $teacher->deactivate();
        $teacher->activate($admin2);
        $teacher->refresh(); // Refresh to get updated relationship
        
        $this->assertEquals($admin2->id, $teacher->activated_by);
        $this->assertEquals('Admin 2', $teacher->activatedBy->name);
    }
}
