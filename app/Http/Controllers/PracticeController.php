<?php

namespace App\Http\Controllers;

use App\Models\FlashcardSet;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PracticeController extends Controller
{
    use AuthorizesRequests;

    /**
     * Start a practice session for a flashcard set.
     */
    public function start(FlashcardSet $flashcardSet)
    {
        $this->authorize('practice', $flashcardSet);

        $flashcards = $flashcardSet->flashcards()->ordered()->get();
        
        // Ensure progress exists
        UserProgress::firstOrCreate(
            ['user_id' => Auth::id(), 'flashcard_set_id' => $flashcardSet->id],
            ['last_accessed' => now()]
        );

        return view('practice.index', compact('flashcardSet', 'flashcards'));
    }

    /**
     * Get the user's current progress for a flashcard set.
     */
    public function getProgress(FlashcardSet $flashcardSet)
    {
        $progress = UserProgress::where('user_id', Auth::id())
            ->where('flashcard_set_id', $flashcardSet->id)
            ->first();

        if ($progress) {
            return response()->json($progress);
        }

        return response()->json(['current_position' => 0, 'completed' => false]);
    }
    
    /**
     * Save the user's progress for a flashcard set.
     */
    public function saveProgress(Request $request, FlashcardSet $flashcardSet)
    {
        $validated = $request->validate([
            'current_position' => 'required|integer|min:0',
            'completed' => 'required|boolean',
        ]);

        $progress = UserProgress::updateOrCreate(
            ['user_id' => Auth::id(), 'flashcard_set_id' => $flashcardSet->id],
            [
                'current_position' => $validated['current_position'],
                'completed' => $validated['completed'],
                'last_accessed' => now(),
            ]
        );

        return response()->json(['message' => 'Progress saved successfully.']);
    }

    /**
     * Restart the practice session.
     */
    public function restart(FlashcardSet $flashcardSet)
    {
        $this->authorize('practice', $flashcardSet);
        
        UserProgress::updateOrCreate(
            ['user_id' => Auth::id(), 'flashcard_set_id' => $flashcardSet->id],
            ['current_position' => 0, 'completed' => false, 'last_accessed' => now()]
        );

        return redirect()->route('practice.index', $flashcardSet);
    }

    /**
     * Save progress and exit practice session.
     */
    public function exitSession(FlashcardSet $flashcardSet)
    {
        session()->forget('practice_set_id');

        return redirect()->route('dashboard');
    }
}
