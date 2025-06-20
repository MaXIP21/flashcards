<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\TeacherAccountActivated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->get('role'), function ($query, $role) {
                $query->where('role', $role);
            })
            ->when($request->get('status'), function ($query, $status) {
                $is_activated = $status === 'active';
                $query->where('is_activated', $is_activated);
            })
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
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
    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
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

    /**
     * Activate the specified teacher.
     */
    public function activate(User $user): RedirectResponse
    {
        if (!$user->isTeacher()) {
            return back()->with('error', 'Only teacher accounts can be activated.');
        }

        $user->activate(auth()->user());

        // Notify the teacher their account is activated
        $user->notify(new TeacherAccountActivated($user));

        return back()->with('success', 'Teacher account activated successfully.');
    }

    /**
     * Deactivate the specified teacher.
     */
    public function deactivate(User $user): RedirectResponse
    {
        if (!$user->isTeacher()) {
            return back()->with('error', 'Only teacher accounts can be deactivated.');
        }

        $user->deactivate();

        // TODO: Add email notification for the user

        return back()->with('success', 'Teacher account deactivated successfully.');
    }
}
