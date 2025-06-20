<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTeacherActivation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // If user is a teacher and not activated, redirect to activation pending page
        if ($user && $user->isTeacher() && !$user->isActivated()) {
            return redirect()->route('activation.pending');
        }

        return $next($request);
    }
}
