<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashcardSet;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FlashcardSetController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', FlashcardSet::class);

        $flashcardSets = FlashcardSet::with('creator')->latest()->paginate(10);

        return view('admin.flashcard-sets.index', compact('flashcardSets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', FlashcardSet::class);

        return view('admin.flashcard-sets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', FlashcardSet::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'source_language' => 'required|string|max:50',
            'target_language' => 'required|string|max:50',
            'is_public' => 'sometimes|boolean',
        ]);

        $request->user()->flashcardSets()->create($validated);

        return redirect()->route('admin.flashcard-sets.index')
            ->with('success', 'Flashcard set created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FlashcardSet $flashcardSet)
    {
        $this->authorize('view', $flashcardSet);

        $flashcardSet->load('flashcards', 'creator');

        return view('admin.flashcard-sets.show', compact('flashcardSet'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FlashcardSet $flashcardSet)
    {
        $this->authorize('update', $flashcardSet);

        return view('admin.flashcard-sets.edit', compact('flashcardSet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FlashcardSet $flashcardSet)
    {
        $this->authorize('update', $flashcardSet);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'source_language' => 'required|string|max:50',
            'target_language' => 'required|string|max:50',
            'is_public' => 'sometimes|boolean',
        ]);

        $validated['is_public'] = $request->has('is_public');

        $flashcardSet->update($validated);

        return redirect()->route('admin.flashcard-sets.index')
            ->with('success', 'Flashcard set updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FlashcardSet $flashcardSet)
    {
        $this->authorize('delete', $flashcardSet);

        $flashcardSet->delete();

        return redirect()->route('admin.flashcard-sets.index')
            ->with('success', 'Flashcard set deleted successfully.');
    }
}
