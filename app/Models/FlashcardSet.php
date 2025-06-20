<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class FlashcardSet extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'source_language',
        'target_language',
        'is_public',
        'unique_identifier',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Boot the model and add event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($flashcardSet) {
            if (empty($flashcardSet->unique_identifier)) {
                $flashcardSet->unique_identifier = Str::random(10);
            }
        });
    }

    /**
     * Get the user who created this flashcard set.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the flashcards in this set.
     */
    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class)->orderBy('position');
    }

    /**
     * Get the assignments for this flashcard set.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get the progress records for this flashcard set.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    /**
     * Scope to get only public flashcard sets.
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to get flashcard sets created by a specific user.
     */
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Check if a flashcard set is assigned to a specific student by a specific teacher.
     */
    public function isAssignedToBy(User $student, User $teacher): bool
    {
        return $this->assignments()
            ->where('student_id', $student->id)
            ->where('teacher_id', $teacher->id)
            ->exists();
    }

    /**
     * Check if the set is assigned to a specific user.
     */
    public function isAssignedTo(User $user): bool
    {
        return $this->assignments()->where('student_id', $user->id)->exists();
    }
}
