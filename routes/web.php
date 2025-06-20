<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\Admin\FlashcardSetController as AdminFlashcardSetController;
use App\Http\Controllers\Admin\FlashcardController as AdminFlashcardController;
use App\Http\Controllers\Teacher\FlashcardSetController as TeacherFlashcardSetController;
use App\Http\Controllers\Teacher\FlashcardController as TeacherFlashcardController;
use App\Http\Controllers\Teacher\AssignmentController as TeacherAssignmentController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ProgressController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Activation pending route - redirect activated teachers
    Route::get('/activation-pending', function () {
        if (auth()->user()->isTeacher() && auth()->user()->is_activated) {
            return redirect()->route('dashboard');
        }
        return view('auth.activation-pending');
    })->name('activation.pending');

    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('flashcard-sets', AdminFlashcardSetController::class);
        Route::resource('flashcard-sets.flashcards', AdminFlashcardController::class);
        Route::post('flashcard-sets/{flashcard_set}/flashcards/bulk-import', [AdminFlashcardController::class, 'bulkImport'])->name('flashcard-sets.flashcards.bulk-import');

        // User Management
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->only(['index', 'show']);
        Route::post('users/{user}/activate', [\App\Http\Controllers\Admin\UserController::class, 'activate'])->name('users.activate');
        Route::post('users/{user}/deactivate', [\App\Http\Controllers\Admin\UserController::class, 'deactivate'])->name('users.deactivate');
    });

    // Teacher routes (require activation)
    Route::middleware(['role:teacher', 'teacher.activated'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::resource('flashcard-sets', TeacherFlashcardSetController::class);
        Route::resource('flashcard-sets.flashcards', TeacherFlashcardController::class)->except(['show']);
        Route::post('flashcard-sets/{flashcard_set}/flashcards/bulk-import', [TeacherFlashcardController::class, 'bulkImport'])->name('flashcard-sets.flashcards.bulk-import');
        Route::resource('assignments', TeacherAssignmentController::class)->only(['index', 'store', 'destroy']);
        Route::get('progress', [ProgressController::class, 'index'])->name('progress.index');
    });

    // Student routes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('assignments', [StudentAssignmentController::class, 'index'])->name('assignments.index');
    });

    // Practice Routes
    Route::prefix('practice')->name('practice.')->group(function () {
        Route::get('/{flashcardSet}', [PracticeController::class, 'index'])->name('index');
        Route::get('/{flashcardSet}/start', [PracticeController::class, 'start'])->name('start');
        Route::post('/{flashcardSet}/restart', [PracticeController::class, 'restart'])->name('restart');
        Route::post('/{flashcardSet}/exit', [PracticeController::class, 'exitSession'])->name('exit');
        Route::get('/{flashcardSet}/progress', [PracticeController::class, 'getProgress'])->name('get-progress');
        Route::post('/{flashcardSet}/progress', [PracticeController::class, 'saveProgress'])->name('save-progress');
    });
});

// Public routes for flashcard sets
Route::prefix('public/sets/{uniqueIdentifier}')->name('public.')->group(function () {
    Route::get('/', [PublicController::class, 'show'])->name('flashcard-set');
    Route::get('/practice', [PublicController::class, 'practice'])->name('practice');
});

require __DIR__.'/auth.php';
