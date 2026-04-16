<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeProjectAccess
{
    /**
     * Handle an incoming request.
     * Ensures user can only access projects they manage (or admins can access any).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $project = $request->route('project');

        if ($project && !auth()->user()->isAdmin() && $project->manager_id !== auth()->id()) {
            abort(403, 'You do not have permission to access this project.');
        }

        return $next($request);
    }
}
