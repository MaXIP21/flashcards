<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flashcard extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'flashcard_set_id',
        'source_word',
        'target_word',
        'position',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'position' => 'integer',
    ];

    /**
     * Get the flashcard set that owns this flashcard.
     */
    public function flashcardSet(): BelongsTo
    {
        return $this->belongsTo(FlashcardSet::class);
    }

    /**
     * Scope to order flashcards by position.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }
}
