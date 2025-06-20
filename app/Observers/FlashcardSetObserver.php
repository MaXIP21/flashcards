<?php

namespace App\Observers;

use App\Models\FlashcardSet;

class FlashcardSetObserver
{
    /**
     * Handle the FlashcardSet "created" event.
     */
    public function created(FlashcardSet $flashcardSet): void
    {
        //
    }

    /**
     * Handle the FlashcardSet "updated" event.
     */
    public function updated(FlashcardSet $flashcardSet): void
    {
        //
    }

    /**
     * Handle the FlashcardSet "deleted" event.
     */
    public function deleted(FlashcardSet $flashcardSet): void
    {
        if ($flashcardSet->isForceDeleting()) {
            $flashcardSet->flashcards()->forceDelete();
        } else {
            $flashcardSet->flashcards()->delete();
        }
    }

    /**
     * Handle the FlashcardSet "restored" event.
     */
    public function restored(FlashcardSet $flashcardSet): void
    {
        $flashcardSet->flashcards()->withTrashed()->restore();
    }

    /**
     * Handle the FlashcardSet "force deleted" event.
     */
    public function forceDeleted(FlashcardSet $flashcardSet): void
    {
        //
    }
}
