<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeTaskAccess
{
    /**
     * Handle an incoming request.
     * Ensures user can only access tasks they're assigned to or that belong to their projects (PMs/Admins).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $task = $request->route('task');

        if ($task) {
            $isTaskAssignee = $task->assigned_user_id === auth()->id();
            $isProjectManager = $task->project->manager_id === auth()->id();
            $isAdmin = auth()->user()->isAdmin();

            if (!$isTaskAssignee && !$isProjectManager && !$isAdmin) {
                abort(403, 'You do not have permission to access this task.');
            }
        }

        return $next($request);
    }
}

