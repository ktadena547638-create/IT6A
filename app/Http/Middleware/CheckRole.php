<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Allows role-based access control on routes.
     * Usage: Route::middleware('checkRole:admin,project_manager')->group(...)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // If user is not authenticated, redirect to login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Get user's role
        $userRole = strtolower(trim((string) auth()->user()->role));
        $roles = array_map(fn ($role) => strtolower(trim((string) $role)), $roles);

        // Check if user's role is in allowed roles
        if (!in_array($userRole, $roles, true)) {
            // User doesn't have required role
            abort(403, 'This action is unauthorized.');
        }

        return $next($request);
    }
}

