<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_activated',
        'activated_at',
        'activated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
            'is_activated' => 'boolean',
            'activated_at' => 'datetime',
        ];
    }

    /**
     * Boot the model and add event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        // Set default activation status based on role
        static::creating(function ($user) {
            if (!isset($user->is_activated)) {
                $user->is_activated = $user->role !== 'teacher';
            }
        });
    }

    /**
     * Get the flashcard sets created by this user.
     */
    public function flashcardSets(): HasMany
    {
        return $this->hasMany(FlashcardSet::class, 'created_by');
    }

    /**
     * Get the assignments where this user is the teacher.
     */
    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'teacher_id');
    }

    /**
     * Get the assignments where this user is the student.
     */
    public function studentAssignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'student_id');
    }

    /**
     * Get the progress records for this user.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    /**
     * Get the admin who activated this user.
     */
    public function activatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    /**
     * Get the users activated by this admin.
     */
    public function activatedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'activated_by');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is teacher.
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Check if user is student.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the specified roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Check if user is activated.
     */
    public function isActivated(): bool
    {
        return $this->is_activated;
    }

    /**
     * Activate the user.
     */
    public function activate(User $admin = null): bool
    {
        $this->update([
            'is_activated' => true,
            'activated_at' => now(),
            'activated_by' => $admin?->id,
        ]);

        return true;
    }

    /**
     * Deactivate the user.
     */
    public function deactivate(): bool
    {
        $this->update([
            'is_activated' => false,
            'activated_at' => null,
            'activated_by' => null,
        ]);

        return true;
    }

    /**
     * Check if user can access teacher features.
     */
    public function canAccessTeacherFeatures(): bool
    {
        return $this->isTeacher() && $this->isActivated();
    }
}
