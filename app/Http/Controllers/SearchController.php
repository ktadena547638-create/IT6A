<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class SearchController extends Controller
{
    public function __construct()
    {
        // ✅ HARDENED: Apply rate limiting - 60 requests per minute
        $this->middleware('throttle:60,1');
    }

    /**
     * Global search for projects and tasks - OPTIMIZED and RATE-LIMITED
     * ✅ HARDENED: Rate limited to 60 requests per minute
     * ✅ HARDENED: Authorization-gated - User can only search their accessible projects
     * ✅ HARDENED: Wrapped with error handling
     */
    public function search(string $query): JsonResponse
    {
        try {
            // ✅ CRITICAL FIX: Authorize user can search
            Gate::authorize('viewAny', Project::class);
            
            if (strlen($query) < 2) {
                return response()->json([
                    'projects' => [],
                    'tasks' => [],
                ]);
            }

            // ✅ CRITICAL FIX: Filter projects by manager OR where user has task assignments
            // User can only search projects they manage or have tasks in
            $user = auth()->user();
            $projects = Project::select(['id', 'name', 'description', 'status'])
                ->where(function ($q) use ($user) {
                    $q->where('manager_id', $user->id)
                      ->orWhereHas('tasks', function ($taskQuery) use ($user) {
                          $taskQuery->where('assigned_user_id', $user->id);
                      });
                })
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->limit(5)
                ->get()
                ->map(function ($project) {
                    return [
                        'id' => $project->id,
                        'title' => $project->name,
                        'description' => substr($project->description ?? '', 0, 60),
                        'type' => 'project',
                        'url' => route('projects.show', $project),
                        'icon' => 'project',
                    ];
                });

            // ✅ CRITICAL FIX: Select only needed columns and filter tasks by user access rights
            // User can only see tasks:
            // 1. Assigned to them, OR
            // 2. In projects they manage
            $tasks = Task::select(['id', 'project_id', 'title', 'description', 'priority'])
                ->with('project:id,name')
                ->where(function ($q) use ($user) {
                    $q->where('assigned_user_id', $user->id)
                      ->orWhereHas('project', function ($pq) use ($user) {
                          $pq->where('manager_id', $user->id);
                      });
                })
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->limit(5)
                ->get()
                ->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'description' => "Project: " . ($task->project?->name ?? 'N/A'),
                        'type' => 'task',
                        'url' => route('tasks.show', $task),
                        'icon' => $task->priority,
                    ];
                });

            return response()->json([
                'projects' => $projects,
                'tasks' => $tasks,
                'query' => $query,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (Exception $e) {
            Log::error('Global search failed', [
                'query' => $query ?? '',
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'projects' => [],
                'tasks' => [],
                'error' => 'Search temporarily unavailable',
            ], 500);
        }
    }
}

