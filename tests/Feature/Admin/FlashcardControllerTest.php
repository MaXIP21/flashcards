<?php

namespace Tests\Feature\Admin;

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
    public function admin_can_view_flashcards_index_for_any_set()
    {
        Flashcard::factory()->create(['flashcard_set_id' => $this->set->id]);

        $this->get(route('admin.flashcard-sets.flashcards.index', $this->set))
            ->assertOk();
    }

    /** @test */
    public function admin_can_view_create_flashcard_page_for_any_set()
    {
        $this->get(route('admin.flashcard-sets.flashcards.create', $this->set))
            ->assertOk();
    }

    /** @test */
    public function admin_can_store_a_new_flashcard_in_any_set()
    {
        $data = [
            'source_word' => 'Hello',
            'target_word' => 'Hola',
        ];

        $this->post(route('admin.flashcard-sets.flashcards.store', $this->set), $data)
            ->assertRedirect();

        $this->assertDatabaseHas('flashcards', [
            'flashcard_set_id' => $this->set->id,
            'source_word' => 'Hello',
        ]);
    }

    /** @test */
    public function admin_can_edit_a_flashcard_in_any_set()
    {
        $flashcard = Flashcard::factory()->create(['flashcard_set_id' => $this->set->id]);

        $this->get(route('admin.flashcard-sets.flashcards.edit', [$this->set, $flashcard]))
            ->assertOk();
    }

    /** @test */
    public function admin_can_update_a_flashcard_in_any_set()
    {
        $flashcard = Flashcard::factory()->create(['flashcard_set_id' => $this->set->id]);
        $updatedData = [
            'source_word' => 'Goodbye',
            'target_word' => 'Adios',
        ];

        $this->put(route('admin.flashcard-sets.flashcards.update', [$this->set, $flashcard]), $updatedData)
            ->assertRedirect();
        
        $this->assertDatabaseHas('flashcards', ['id' => $flashcard->id, 'source_word' => 'Goodbye']);
    }

    /** @test */
    public function admin_can_delete_a_flashcard_from_any_set()
    {
        $flashcard = Flashcard::factory()->create(['flashcard_set_id' => $this->set->id]);

        $this->delete(route('admin.flashcard-sets.flashcards.destroy', [$this->set, $flashcard]))
            ->assertRedirect();
        
        $this->assertSoftDeleted('flashcards', ['id' => $flashcard->id]);
    }

    /** @test */
    public function admin_can_bulk_import_flashcards_to_any_set()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent(
            'flashcards.csv',
            "source,target\nHello,Hola\nGoodbye,Adios"
        );

        $this->post(route('admin.flashcard-sets.flashcards.bulk-import', $this->set), [
            'import_file' => $file,
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('flashcards', ['source_word' => 'Hello']);
        $this->assertDatabaseHas('flashcards', ['source_word' => 'Goodbye']);
    }
    
    /** @test */
    public function non_admin_cannot_access_admin_flashcard_routes()
    {
        $this->actingAs($this->teacher);
        $flashcard = Flashcard::factory()->create(['flashcard_set_id' => $this->set->id]);

        $this->get(route('admin.flashcard-sets.flashcards.index', $this->set))->assertForbidden();
        $this->get(route('admin.flashcard-sets.flashcards.create', $this->set))->assertForbidden();
        $this->post(route('admin.flashcard-sets.flashcards.store', $this->set), [])->assertForbidden();
        $this->get(route('admin.flashcard-sets.flashcards.edit', [$this->set, $flashcard]))->assertForbidden();
        $this->put(route('admin.flashcard-sets.flashcards.update', [$this->set, $flashcard]), [])->assertForbidden();
        $this->delete(route('admin.flashcard-sets.flashcards.destroy', [$this->set, $flashcard]))->assertForbidden();
        $this->post(route('admin.flashcard-sets.flashcards.bulk-import', $this->set), [])->assertForbidden();
    }
}
