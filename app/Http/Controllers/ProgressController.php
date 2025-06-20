<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProgressController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', UserProgress::class);

        $students = User::where('role', 'student')->get();
        
        $assignments = Assignment::where('teacher_id', Auth::id())
            ->with(['student', 'flashcardSet'])
            ->get();
            
        $studentProgress = UserProgress::whereIn('user_id', $assignments->pluck('student_id'))
            ->whereIn('flashcard_set_id', $assignments->pluck('flashcard_set_id'))
            ->with(['user', 'flashcardSet'])
            ->get()
            ->groupBy('user_id');

        return view('teacher.progress.index', compact('students', 'studentProgress', 'assignments'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
