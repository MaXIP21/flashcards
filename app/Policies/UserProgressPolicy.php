<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Auth\Access\Response;

class UserProgressPolicy
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
        return $user->isTeacher();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserProgress $userProgress): bool
    {
        // Student can view their own progress
        if ($user->id === $userProgress->user_id) {
            return true;
        }

        // Teacher can view progress of their students on assigned sets
        if ($user->isTeacher()) {
            return $userProgress->flashcardSet->isAssignedToBy($userProgress->user_id, $user->id);
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Progress is created automatically
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserProgress $userProgress): bool
    {
        return $user->id === $userProgress->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserProgress $userProgress): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserProgress $userProgress): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserProgress $userProgress): bool
    {
        return false;
    }
}
