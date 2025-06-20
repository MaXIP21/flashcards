<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewTeacherRegistered;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', 'string', 'in:student,teacher'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'is_activated' => $request->role !== 'teacher', // Teachers start as non-activated
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role and activation status
        if ($user->isTeacher() && !$user->isActivated()) {
            // Notify admins of new teacher registration
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new NewTeacherRegistered($user));

            return redirect()->route('activation.pending')
                ->with('success', 'Registration successful! Your account is pending activation by an administrator. You will be notified via email once your account is activated.');
        }

        return redirect(route('dashboard', absolute: false))
            ->with('success', 'Registration successful! Welcome to the platform.');
    }
}
