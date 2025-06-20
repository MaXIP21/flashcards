<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'teacher_id',
        'student_id',
        'flashcard_set_id',
        'assigned_at',
        'due_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'assigned_at' => 'datetime',
        'due_date' => 'datetime',
    ];

    /**
     * Get the teacher who assigned this assignment.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the student assigned to this assignment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the flashcard set for this assignment.
     */
    public function flashcardSet(): BelongsTo
    {
        return $this->belongsTo(FlashcardSet::class);
    }

    /**
     * Scope to get assignments for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get assignments by a specific teacher.
     */
    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Scope to get assignments that are due.
     */
    public function scopeDue($query)
    {
        return $query->where('due_date', '<=', now());
    }
}
