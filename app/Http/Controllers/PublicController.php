<?php

namespace App\Http\Controllers;

use App\Models\FlashcardSet;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Display a public flashcard set.
     */
    public function show(string $uniqueIdentifier)
    {
        $flashcardSet = FlashcardSet::where('unique_identifier', $uniqueIdentifier)->first();

        if (!$flashcardSet || !$flashcardSet->is_public) {
            abort(404);
        }
        
        $flashcardSet->load('flashcards')->loadCount('flashcards');

        return view('public.flashcard-set', compact('flashcardSet'));
    }

    /**
     * Start a practice session for a public flashcard set.
     */
    public function practice(string $uniqueIdentifier)
    {
        $flashcardSet = FlashcardSet::where('unique_identifier', $uniqueIdentifier)->first();

        if (!$flashcardSet || !$flashcardSet->is_public) {
            abort(404);
        }

        $flashcards = $flashcardSet->flashcards()->ordered()->get();

        // For guests, progress is not saved, so we pass an empty progress object.
        return view('public.practice', compact('flashcardSet', 'flashcards'));
    }
}
