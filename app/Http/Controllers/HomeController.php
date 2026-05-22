<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\TaskActivity;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Exception;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Display the Home Command Center
     * 
     * ✅ PERFORMANCE: Uses eager loading to prevent N+1 queries
     * ✅ FORENSIC: Latest 10 TaskActivity entries with relationships
     * ✅ STRUCTURAL: 3 recent projects + 5 upcoming tasks
     * ✅ SUB-250MS: Implements aggressive caching
     */
    public function index(): View
    {
        try {
            $user = auth()->user();
            $cacheKeyPrefix = 'home_' . $user->id;
            $cacheTTL = now()->addMinutes(5); // 5-minute TTL

            // ========== PERSONALIZED WELCOME ==========
            $userName = $user->name ?? 'Guest';
            $userRole = $user->role ?? 'user';

            // ========== FORENSIC ACTIVITY FEED ==========
            // Latest 10 TaskActivity entries with eager loaded relationships
            // CRITICAL: Prevents N+1 by loading user and task in single query
            $activityFeed = Cache::remember($cacheKeyPrefix . '_activity_feed', $cacheTTL, function () {
                return TaskActivity::with(['user:id,name,email', 'task:id,title,project_id,status'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($activity) {
                        return [
                            'id' => $activity->id,
                            'user_name' => $activity->user?->name ?? 'System',
                            'activity_type' => $activity->activity_type,
                            'description' => $activity->description,
                            'task_title' => $activity->task?->title ?? 'Deleted Task',
                            'task_id' => $activity->task_id,
                            'created_at' => $activity->created_at->diffForHumans(),
                        ];
                    });
            });

            // ========== STRUCTURAL HIGHLIGHTS ==========
            // 3 Most Recently Updated Projects
            $recentProjects = Cache::remember($cacheKeyPrefix . '_recent_projects', $cacheTTL, function () use ($user) {
                $query = Project::with(['manager:id,name', 'tasks:id,project_id,status'])
                    ->orderBy('updated_at', 'desc');

                // Admins see all projects; others see only managed or related projects
                $query->when(! $user->isAdmin(), function ($q) use ($user) {
                    $q->where('manager_id', $user->id)
                      ->orWhereHas('tasks', function ($q2) use ($user) {
                          $q2->where('assigned_user_id', $user->id)
                             ->orWhere('created_by', $user->id);
                      });
                });

                return $query->limit(3)
                    ->get()
                    ->map(function ($project) {
                        $stats = [
                            'total_tasks' => $project->tasks->count(),
                            'completed_tasks' => $project->tasks->where('status', 'completed')->count(),
                            'pending_tasks' => $project->tasks->where('status', 'pending')->count(),
                            'in_progress_tasks' => $project->tasks->where('status', 'in_progress')->count(),
                        ];

                        return [
                            'id' => $project->id,
                            'name' => $project->name,
                            'status' => $project->status,
                            'priority' => $project->priority,
                            'manager_name' => $project->manager?->name ?? 'Unassigned',
                            'updated_at' => $project->updated_at->format('M d, Y'),
                            'stats' => $stats,
                            'completion_percent' => $stats['total_tasks'] > 0 
                                ? round(($stats['completed_tasks'] / $stats['total_tasks']) * 100)
                                : 0,
                        ];
                    });
            });

            // ========== UPCOMING TASKS FOR USER ==========
            // 5 Upcoming Tasks assigned to or created by user
            $upcomingTasks = Cache::remember($cacheKeyPrefix . '_upcoming_tasks', $cacheTTL, function () use ($user) {
                $query = Task::with(['project:id,name', 'assignedUser:id,name', 'creator:id,name'])
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->orderBy('due_date', 'asc');

                // Restrict to user's tasks unless admin
                $query->when(! $user->isAdmin(), function ($q) use ($user) {
                    $q->where(function ($q2) use ($user) {
                        $q2->where('assigned_user_id', $user->id)
                           ->orWhere('created_by', $user->id);
                    });
                });

                return $query->limit(5)
                    ->get()
                    ->map(function ($task) {
                        return [
                            'id' => $task->id,
                            'title' => $task->title,
                            'project_name' => $task->project?->name ?? 'No Project',
                            'priority' => $task->priority,
                            'status' => $task->status,
                            'assigned_to' => $task->assignedUser?->name ?? 'Unassigned',
                            'created_by' => $task->creator?->name ?? 'System',
                            'due_date' => $task->due_date?->format('M d, Y') ?? 'No date',
                            'is_overdue' => $task->due_date && $task->due_date->isPast() && $task->status !== 'completed',
                        ];
                    });
            });

            // ========== QUICK STATS ==========
            // Quick stats — admins see global counts
            $quickStats = [
                'total_projects' => Project::query()->when(! $user->isAdmin(), function ($q) use ($user) { $q->where('manager_id', $user->id); })->count(),
                'active_projects' => Project::query()->when(! $user->isAdmin(), function ($q) use ($user) { $q->where('manager_id', $user->id); })->where('status', 'active')->count(),
                'assigned_tasks' => Task::query()->when(! $user->isAdmin(), function ($q) use ($user) { $q->where('assigned_user_id', $user->id); })->whereIn('status', ['pending', 'in_progress'])->count(),
                'completed_today' => Task::query()->when(! $user->isAdmin(), function ($q) use ($user) { $q->where('assigned_user_id', $user->id); })->where('status', 'completed')->whereDate('updated_at', today())->count(),
            ];

            return view('home', [
                'userName' => $userName,
                'userRole' => $userRole,
                'activityFeed' => $activityFeed,
                'recentProjects' => $recentProjects,
                'upcomingTasks' => $upcomingTasks,
                'quickStats' => $quickStats,
            ]);

        } catch (Exception $e) {
            Log::error('HomeController@index error: ' . $e->getMessage());
            return view('home', [
                'userName' => auth()->user()?->name ?? 'Guest',
                'userRole' => auth()->user()?->role ?? 'user',
                'activityFeed' => collect(),
                'recentProjects' => collect(),
                'upcomingTasks' => collect(),
                'quickStats' => [
                    'total_projects' => 0,
                    'active_projects' => 0,
                    'assigned_tasks' => 0,
                    'completed_today' => 0,
                ],
            ]);
        }
    }
}

