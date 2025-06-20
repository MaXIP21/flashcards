<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = Assignment::where('student_id', Auth::id())
            ->with(['teacher', 'flashcardSet' => function ($query) {
                $query->withCount('flashcards');
            }])
            ->latest('assigned_at')
            ->paginate(15);

        return view('student.assignments.index', compact('assignments'));
    }
}
