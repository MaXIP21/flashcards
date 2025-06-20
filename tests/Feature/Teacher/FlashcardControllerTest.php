<?php

namespace Tests\Feature\Teacher;

use App\Models\Flashcard;
use App\Models\FlashcardSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FlashcardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacher;
    protected FlashcardSet $set;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teacher = User::factory()->teacher()->create();
        $this->set = FlashcardSet::factory()->create(['created_by' => $this->teacher->id]);
        $this->actingAs($this->teacher);
    }

    /** @test */
    public function teacher_can_view_flashcards_index_for_their_set()
    {
        Flashcard::factory()->create(['flashcard_set_id' => $this->set->id]);

        $this->get(route('teacher.flashcard-sets.flashcards.index', $this->set))
            ->assertOk();
    }

    /** @test */
    public function teacher_can_view_create_flashcard_page_for_their_set()
    {
        $this->get(route('teacher.flashcard-sets.flashcards.create', $this->set))
            ->assertOk();
    }

    /** @test */
    public function teacher_can_store_a_new_flashcard_in_their_set()
    {
        $data = [
            'source_word' => 'Hello',
            'target_word' => 'Hola',
        ];

        $this->post(route('teacher.flashcard-sets.flashcards.store', $this->set), $data)
            ->assertRedirect();

        $this->assertDatabaseHas('flashcards', [
            'flashcard_set_id' => $this->set->id,
            'source_word' => 'Hello',
        ]);
    }

    /** @test */
    public function teacher_can_edit_a_flashcard_in_their_set()
    {
        $flashcard = Flashcard::factory()->create(['flashcard_set_id' => $this->set->id]);

        $this->get(route('teacher.flashcard-sets.flashcards.edit', [$this->set, $flashcard]))
            ->assertOk();
    }

    /** @test */
    public function teacher_can_update_a_flashcard_in_their_set()
    {
        $flashcard = Flashcard::factory()->create(['flashcard_set_id' => $this->set->id]);
        $updatedData = [
            'source_word' => 'Goodbye',
            'target_word' => 'Adios',
        ];

        $this->put(route('teacher.flashcard-sets.flashcards.update', [$this->set, $flashcard]), $updatedData)
            ->assertRedirect();
        
        $this->assertDatabaseHas('flashcards', ['id' => $flashcard->id, 'source_word' => 'Goodbye']);
    }

    /** @test */
    public function teacher_can_delete_a_flashcard_from_their_set()
    {
        $flashcard = Flashcard::factory()->create(['flashcard_set_id' => $this->set->id]);

        $this->delete(route('teacher.flashcard-sets.flashcards.destroy', [$this->set, $flashcard]))
            ->assertRedirect();
        
        $this->assertSoftDeleted('flashcards', ['id' => $flashcard->id]);
    }

    /** @test */
    public function teacher_cannot_access_flashcard_pages_for_another_teachers_set()
    {
        $otherSet = FlashcardSet::factory()->create(['is_public' => false]); // Belongs to another teacher
        $flashcard = Flashcard::factory()->create(['flashcard_set_id' => $otherSet->id]);

        $this->get(route('teacher.flashcard-sets.flashcards.index', $otherSet))->assertForbidden();
        $this->get(route('teacher.flashcard-sets.flashcards.create', $otherSet))->assertForbidden();
        $this->post(route('teacher.flashcard-sets.flashcards.store', $otherSet), [])->assertForbidden();
        $this->get(route('teacher.flashcard-sets.flashcards.edit', [$otherSet, $flashcard]))->assertForbidden();
        $this->put(route('teacher.flashcard-sets.flashcards.update', [$otherSet, $flashcard]), [])->assertForbidden();
        $this->delete(route('teacher.flashcard-sets.flashcards.destroy', [$otherSet, $flashcard]))->assertForbidden();
    }

    /** @test */
    public function teacher_can_bulk_import_flashcards_from_csv()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent(
            'flashcards.csv',
            "source,target\nHello,Hola\nGoodbye,Adios"
        );

        $this->post(route('teacher.flashcard-sets.flashcards.bulk-import', $this->set), [
            'import_file' => $file,
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('flashcards', ['source_word' => 'Hello']);
        $this->assertDatabaseHas('flashcards', ['source_word' => 'Goodbye']);
    }

    /** @test */
    public function teacher_can_bulk_import_flashcards_from_json()
    {
        Storage::fake('local');
        $data = [
            ['source' => 'Cat', 'target' => 'Gato'],
            ['source' => 'Dog', 'target' => 'Perro'],
        ];
        $file = UploadedFile::fake()->createWithContent(
            'flashcards.json',
            json_encode($data)
        );

        $this->post(route('teacher.flashcard-sets.flashcards.bulk-import', $this->set), [
            'import_file' => $file,
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('flashcards', ['source_word' => 'Cat']);
        $this->assertDatabaseHas('flashcards', ['source_word' => 'Dog']);
    }
}
