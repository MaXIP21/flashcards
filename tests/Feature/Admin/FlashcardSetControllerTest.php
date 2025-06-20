<?php

namespace Tests\Feature\Admin;

use App\Models\FlashcardSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashcardSetControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected FlashcardSet $set;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->teacher = User::factory()->teacher()->create();
        $this->set = FlashcardSet::factory()->create(['created_by' => $this->teacher->id]);
        $this->actingAs($this->admin);
    }

    /** @test */
    public function admin_can_view_all_flashcard_sets()
    {
        $this->get(route('admin.flashcard-sets.index'))
            ->assertOk()
            ->assertSee($this->set->title);
    }

    /** @test */
    public function admin_can_view_any_flashcard_set()
    {
        $this->get(route('admin.flashcard-sets.show', $this->set))
            ->assertOk()
            ->assertSee($this->set->title);
    }

    /** @test */
    public function admin_can_update_any_flashcard_set()
    {
        $updatedData = [
            'title' => 'Admin Updated Title',
            'description' => 'Admin updated description.',
            'source_language' => 'fr',
            'target_language' => 'de',
        ];

        $this->put(route('admin.flashcard-sets.update', $this->set), $updatedData)
            ->assertRedirect();
        
        $this->assertDatabaseHas('flashcard_sets', ['id' => $this->set->id, 'title' => 'Admin Updated Title']);
    }

    /** @test */
    public function admin_can_delete_any_flashcard_set()
    {
        $this->delete(route('admin.flashcard-sets.destroy', $this->set))
            ->assertRedirect();

        $this->assertSoftDeleted('flashcard_sets', ['id' => $this->set->id]);
    }

    /** @test */
    public function non_admin_cannot_access_admin_routes()
    {
        $this->actingAs($this->teacher);

        $this->get(route('admin.flashcard-sets.index'))->assertForbidden();
        $this->get(route('admin.flashcard-sets.show', $this->set))->assertForbidden();
        $this->put(route('admin.flashcard-sets.update', $this->set), [])->assertForbidden();
        $this->delete(route('admin.flashcard-sets.destroy', $this->set))->assertForbidden();
    }
}
