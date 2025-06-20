<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\FlashcardSet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AssignmentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Assignment::class);

        $assignments = Assignment::where('teacher_id', Auth::id())
            ->with(['student', 'flashcardSet'])
            ->latest()
            ->paginate(15);
        
        $flashcardSets = FlashcardSet::where('created_by', Auth::id())->get();
        $students = User::where('role', 'student')->get();

        return view('teacher.assignments.index', compact('assignments', 'flashcardSets', 'students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Assignment::class);

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'flashcard_set_id' => 'required|exists:flashcard_sets,id',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        // Ensure teacher can only assign their own sets
        $flashcardSet = FlashcardSet::findOrFail($validated['flashcard_set_id']);
        if ($flashcardSet->created_by !== Auth::id()) {
            abort(403, 'You can only assign your own flashcard sets.');
        }

        Assignment::create([
            'teacher_id' => Auth::id(),
            'student_id' => $validated['student_id'],
            'flashcard_set_id' => $validated['flashcard_set_id'],
            'due_date' => $validated['due_date'],
            'assigned_at' => now(),
        ]);

        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Assignment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assignment $assignment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assignment $assignment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        $this->authorize('delete', $assignment);

        $assignment->delete();

        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Assignment removed successfully.');
    }
}
