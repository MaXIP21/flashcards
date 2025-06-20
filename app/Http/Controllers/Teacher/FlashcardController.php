<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Flashcard;
use App\Models\FlashcardSet;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FlashcardController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(FlashcardSet $flashcardSet)
    {
        $this->authorize('viewAny', Flashcard::class);
        $this->authorize('view', $flashcardSet);

        $flashcards = $flashcardSet->flashcards()->ordered()->paginate(20);

        return view('teacher.flashcards.index', compact('flashcardSet', 'flashcards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(FlashcardSet $flashcardSet)
    {
        $this->authorize('create', Flashcard::class);
        $this->authorize('view', $flashcardSet);

        return view('teacher.flashcards.create', compact('flashcardSet'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, FlashcardSet $flashcardSet)
    {
        $this->authorize('create', Flashcard::class);
        $this->authorize('view', $flashcardSet);

        $validated = $request->validate([
            'source_word' => 'required|string|max:255',
            'target_word' => 'required|string|max:255',
            'position' => 'nullable|integer|min:0',
        ]);

        // If no position is provided, add to the end
        if (!isset($validated['position'])) {
            $validated['position'] = $flashcardSet->flashcards()->max('position') + 1;
        }

        $flashcardSet->flashcards()->create($validated);

        return redirect()->route('teacher.flashcard-sets.show', $flashcardSet)
            ->with('success', 'Flashcard added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FlashcardSet $flashcardSet, Flashcard $flashcard)
    {
        $this->authorize('view', $flashcard);

        return view('teacher.flashcards.show', compact('flashcardSet', 'flashcard'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FlashcardSet $flashcardSet, Flashcard $flashcard)
    {
        $this->authorize('update', $flashcard);

        return view('teacher.flashcards.edit', compact('flashcardSet', 'flashcard'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FlashcardSet $flashcardSet, Flashcard $flashcard)
    {
        $this->authorize('update', $flashcard);

        $validated = $request->validate([
            'source_word' => 'required|string|max:255',
            'target_word' => 'required|string|max:255',
            'position' => 'nullable|integer|min:0',
        ]);

        $flashcard->update($validated);

        return redirect()->route('teacher.flashcard-sets.show', $flashcardSet)
            ->with('success', 'Flashcard updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FlashcardSet $flashcardSet, Flashcard $flashcard)
    {
        $this->authorize('delete', $flashcard);

        $flashcard->delete();

        return redirect()->route('teacher.flashcard-sets.show', $flashcardSet)
            ->with('success', 'Flashcard deleted successfully.');
    }

    /**
     * Bulk import flashcards from CSV/JSON.
     */
    public function bulkImport(Request $request, FlashcardSet $flashcardSet)
    {
        $this->authorize('create', Flashcard::class);
        $this->authorize('view', $flashcardSet);

        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt,json|max:2048',
        ]);

        $file = $request->file('import_file');
        $extension = $file->getClientOriginalExtension();

        if ($extension === 'json') {
            $this->importFromJson($file, $flashcardSet);
        } else {
            $this->importFromCsv($file, $flashcardSet);
        }

        return redirect()->route('teacher.flashcard-sets.show', $flashcardSet)
            ->with('success', 'Flashcards imported successfully.');
    }

    /**
     * Import flashcards from JSON file.
     */
    private function importFromJson($file, FlashcardSet $flashcardSet)
    {
        $content = file_get_contents($file->getPathname());
        $data = json_decode($content, true);

        if (!is_array($data)) {
            throw new \Exception('Invalid JSON format');
        }

        $position = $flashcardSet->flashcards()->max('position') + 1;

        foreach ($data as $item) {
            $source = $item['source_word'] ?? $item['source'] ?? null;
            $target = $item['target_word'] ?? $item['target'] ?? null;

            if ($source && $target) {
                $flashcardSet->flashcards()->create([
                    'source_word' => $source,
                    'target_word' => $target,
                    'position' => $position++,
                ]);
            }
        }
    }

    /**
     * Import flashcards from CSV file.
     */
    private function importFromCsv($file, FlashcardSet $flashcardSet)
    {
        $handle = fopen($file->getPathname(), 'r');
        // Read header row
        $header = fgetcsv($handle);
        
        $position = $flashcardSet->flashcards()->max('position') + 1;

        // Find source and target columns
        $sourceIndex = array_search('source', $header);
        if ($sourceIndex === false) {
            $sourceIndex = array_search('source_word', $header);
        }

        $targetIndex = array_search('target', $header);
        if ($targetIndex === false) {
            $targetIndex = array_search('target_word', $header);
        }

        if ($sourceIndex === false || $targetIndex === false) {
             fclose($handle);
             throw new \Exception('CSV must have "source" and "target" (or "source_word", "target_word") columns.');
        }


        while (($data = fgetcsv($handle)) !== false) {
            if (isset($data[$sourceIndex]) && isset($data[$targetIndex])) {
                $flashcardSet->flashcards()->create([
                    'source_word' => trim($data[$sourceIndex]),
                    'target_word' => trim($data[$targetIndex]),
                    'position' => $position++,
                ]);
            }
        }

        fclose($handle);
    }
}
