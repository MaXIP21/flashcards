<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the user's dashboard based on their role.
     */
    public function index()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
                return $this->adminDashboard();
            case 'teacher':
                return $this->teacherDashboard();
            case 'student':
                return $this->studentDashboard();
            default:
                abort(403, 'Invalid user role.');
        }
    }

    /**
     * Admin dashboard - shows all flashcard sets and system statistics.
     */
    private function adminDashboard()
    {
        $flashcardSets = \App\Models\FlashcardSet::with('creator')
            ->withCount('flashcards')
            ->latest()
            ->paginate(10);

        $stats = [
            'total_sets' => \App\Models\FlashcardSet::count(),
            'total_flashcards' => \App\Models\Flashcard::count(),
            'total_users' => \App\Models\User::count(),
            'public_sets' => \App\Models\FlashcardSet::where('is_public', true)->count(),
        ];

        return view('admin.dashboard', compact('flashcardSets', 'stats'));
    }

    /**
     * Teacher dashboard - shows created sets and assigned students.
     */
    private function teacherDashboard()
    {
        $flashcardSets = \App\Models\FlashcardSet::where('created_by', Auth::id())
            ->withCount('flashcards')
            ->latest()
            ->paginate(10);

        $publicSets = \App\Models\FlashcardSet::where('is_public', true)
            ->withCount('flashcards')
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'my_sets' => \App\Models\FlashcardSet::where('created_by', Auth::id())->count(),
            'total_flashcards' => \App\Models\Flashcard::whereHas('flashcardSet', function($query) {
                $query->where('created_by', Auth::id());
            })->count(),
            'public_sets_available' => \App\Models\FlashcardSet::where('is_public', true)->count(),
        ];

        return view('teacher.dashboard', compact('flashcardSets', 'publicSets', 'stats'));
    }

    /**
     * Student dashboard - shows assigned sets and public sets.
     */
    private function studentDashboard()
    {
        $assignedSets = \App\Models\Assignment::where('student_id', Auth::id())
            ->with(['flashcardSet' => function($query) {
                $query->withCount('flashcards');
            }])
            ->with('teacher')
            ->latest()
            ->get();

        $publicSets = \App\Models\FlashcardSet::where('is_public', true)
            ->withCount('flashcards')
            ->latest()
            ->take(10)
            ->get();

        $recentProgress = \App\Models\UserProgress::where('user_id', Auth::id())
            ->with('flashcardSet')
            ->latest('last_accessed')
            ->take(5)
            ->get();

        $stats = [
            'assigned_sets' => $assignedSets->count(),
            'completed_sets' => \App\Models\UserProgress::where('user_id', Auth::id())
                ->where('completed', true)
                ->count(),
            'public_sets_available' => $publicSets->count(),
        ];

        return view('student.dashboard', compact('assignedSets', 'publicSets', 'recentProgress', 'stats'));
    }
}
