<?php

namespace Tests\Feature\Teacher;

use App\Models\FlashcardSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashcardSetControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacher;
    protected User $otherTeacher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teacher = User::factory()->teacher()->create();
        $this->otherTeacher = User::factory()->teacher()->create();
        $this->actingAs($this->teacher);
    }

    /** @test */
    public function teacher_can_view_their_own_flashcard_sets_index()
    {
        $set = FlashcardSet::factory()->create(['created_by' => $this->teacher->id]);
        $otherSet = FlashcardSet::factory()->create(['created_by' => $this->otherTeacher->id]);

        $this->get(route('teacher.flashcard-sets.index'))
            ->assertOk()
            ->assertSee($set->title)
            ->assertDontSee($otherSet->title);
    }

    /** @test */
    public function teacher_can_view_the_create_flashcard_set_page()
    {
        $this->get(route('teacher.flashcard-sets.create'))
            ->assertOk();
    }

    /** @test */
    public function teacher_can_store_a_new_flashcard_set()
    {
        $data = FlashcardSet::factory()->make()->toArray();
        $data['created_by'] = $this->teacher->id;

        $this->post(route('teacher.flashcard-sets.store'), $data)
            ->assertRedirect(route('teacher.flashcard-sets.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('flashcard_sets', ['title' => $data['title']]);
    }

    /** @test */
    public function teacher_can_view_their_own_flashcard_set()
    {
        $set = FlashcardSet::factory()->create(['created_by' => $this->teacher->id]);

        $this->get(route('teacher.flashcard-sets.show', $set))
            ->assertOk()
            ->assertSee($set->title);
    }

    /** @test */
    public function teacher_cannot_view_another_teachers_flashcard_set()
    {
        $otherSet = FlashcardSet::factory()->create(['created_by' => $this->otherTeacher->id, 'is_public' => false]);

        $this->get(route('teacher.flashcard-sets.show', $otherSet))
            ->assertForbidden();
    }

    /** @test */
    public function teacher_can_update_their_own_flashcard_set()
    {
        $set = FlashcardSet::factory()->create(['created_by' => $this->teacher->id]);
        $updatedData = [
            'title' => 'New Title',
            'description' => 'New Description',
            'source_language' => 'en',
            'target_language' => 'es',
        ];

        $this->put(route('teacher.flashcard-sets.update', $set), $updatedData)
            ->assertRedirect(route('teacher.flashcard-sets.show', $set))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('flashcard_sets', ['id' => $set->id, 'title' => 'New Title']);
    }

    /** @test */
    public function teacher_cannot_update_another_teachers_flashcard_set()
    {
        $otherSet = FlashcardSet::factory()->create(['created_by' => $this->otherTeacher->id]);
        $updatedData = [
            'title' => 'New Title',
            'source_language' => 'en',
            'target_language' => 'es',
        ];

        $this->put(route('teacher.flashcard-sets.update', $otherSet), $updatedData)
            ->assertForbidden();
    }

    /** @test */
    public function teacher_can_delete_their_own_flashcard_set()
    {
        $set = FlashcardSet::factory()->create(['created_by' => $this->teacher->id]);

        $this->delete(route('teacher.flashcard-sets.destroy', $set))
            ->assertRedirect(route('teacher.flashcard-sets.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('flashcard_sets', ['id' => $set->id]);
    }

    /** @test */
    public function teacher_cannot_delete_another_teachers_flashcard_set()
    {
        $otherSet = FlashcardSet::factory()->create(['created_by' => $this->otherTeacher->id]);

        $this->delete(route('teacher.flashcard-sets.destroy', $otherSet))
            ->assertForbidden();
    }
}
