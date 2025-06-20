<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProgress extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_progress';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'flashcard_set_id',
        'current_position',
        'completed',
        'last_accessed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'current_position' => 'integer',
        'completed' => 'boolean',
        'last_accessed' => 'datetime',
    ];

    /**
     * Get the user for this progress record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the flashcard set for this progress record.
     */
    public function flashcardSet(): BelongsTo
    {
        return $this->belongsTo(FlashcardSet::class);
    }

    /**
     * Scope to get progress for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get progress for a specific flashcard set.
     */
    public function scopeForFlashcardSet($query, $flashcardSetId)
    {
        return $query->where('flashcard_set_id', $flashcardSetId);
    }

    /**
     * Scope to get completed progress records.
     */
    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    /**
     * Scope to get incomplete progress records.
     */
    public function scopeIncomplete($query)
    {
        return $query->where('completed', false);
    }
}
