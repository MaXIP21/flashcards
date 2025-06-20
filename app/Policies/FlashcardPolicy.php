<?php

namespace App\Policies;

use App\Models\Flashcard;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FlashcardPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isTeacher() || $user->isStudent();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Flashcard $flashcard): bool
    {
        return $flashcard->flashcardSet->is_public || $user->id === $flashcard->flashcardSet->created_by;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Flashcard $flashcard): bool
    {
        return $user->id === $flashcard->flashcardSet->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Flashcard $flashcard): bool
    {
        return $user->id === $flashcard->flashcardSet->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Flashcard $flashcard): bool
    {
        return $user->id === $flashcard->flashcardSet->created_by;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Flashcard $flashcard): bool
    {
        return $user->id === $flashcard->flashcardSet->created_by;
    }
}
