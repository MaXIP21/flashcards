<?php

namespace App\Policies;

use App\Models\FlashcardSet;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FlashcardSetPolicy
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
    public function view(?User $user, FlashcardSet $flashcardSet): bool
    {
        if ($flashcardSet->is_public) {
            return true;
        }

        return $user && ($user->id === $flashcardSet->created_by || $flashcardSet->isAssignedTo($user));
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
    public function update(User $user, FlashcardSet $flashcardSet): bool
    {
        return $user->id === $flashcardSet->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FlashcardSet $flashcardSet): bool
    {
        return $user->id === $flashcardSet->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FlashcardSet $flashcardSet): bool
    {
        return $user->id === $flashcardSet->created_by;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FlashcardSet $flashcardSet): bool
    {
        return $user->id === $flashcardSet->created_by;
    }

    /**
     * Determine whether the user can practice the model.
     */
    public function practice(?User $user, FlashcardSet $flashcardSet): bool
    {
        if ($flashcardSet->is_public) {
            return true;
        }

        return $user && ($user->id === $flashcardSet->created_by || $flashcardSet->isAssignedTo($user));
    }
}
