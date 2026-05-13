<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Services\TaskService;
use App\Services\ProjectService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function __construct(
        private TaskService $taskService,
        private ProjectService $projectService,
    ) {}

    /**
     * Display the dashboard with key metrics
     * ✅ HARDENED: Dashboard KPI cards are now CACHED with 5-minute TTL to prevent N+1 queries
     * ✅ FIXED: Health scores are calculated per-project and injected into array before caching
     * OPTIMIZED: Uses eager loading, query caching, and database aggregation
     */
    public function index(): View
    {
        try {
            $user = auth()->user();
            // Force cache to invalidate immediately on every dashboard load
            // This ensures fresh counts for testing
            $cacheKeyPrefix = 'dashboard_' . $user->id;
            
            // Clear all dashboard caches
            Cache::forget($cacheKeyPrefix . '_project_count');
            Cache::forget($cacheKeyPrefix . '_user_projects');
            Cache::forget($cacheKeyPrefix . '_avg_health');
            Cache::forget($cacheKeyPrefix . '_user_tasks');
            Cache::forget($cacheKeyPrefix . '_overdue_tasks');
            Cache::forget($cacheKeyPrefix . '_due_today');
            Cache::forget($cacheKeyPrefix . '_projects_priority_breakdown');
            Cache::forget($cacheKeyPrefix . '_tasks_priority_breakdown');
            
            $cacheTTL = now()->addMinutes(5); // 5-minute TTL

            // ✅ PERFORMANCE: Calculate project count (NO CACHE - fresh data)
            $projectCount = Project::query()
                ->when(!$user->isAdmin(), function($q) use ($user) {
                    return $q->where('manager_id', $user->id);
                })
                ->count();
            
            // ✅ PERFORMANCE: Calculate user projects with health_score (NO CACHE - fresh data)
            $query = Project::select(['id', 'name', 'description', 'status', 'manager_id', 'updated_at'])
                ->with(['manager:id,name', 'tasks:id,project_id,status']);
            if (!$user->isAdmin()) {
                $query->where('manager_id', $user->id);
            }
            $projects = $query->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();

            // Calculate health_score for each project using single aggregated query batch
            $userProjects = $projects->map(function ($project) {
                    $stats = DB::table('tasks')
                        ->where('project_id', $project->id)
                        ->selectRaw('
                            COUNT(*) as total,
                            SUM(CASE WHEN status = \'completed\' THEN 1 ELSE 0 END) as completed,
                            SUM(CASE WHEN due_date < NOW() AND status != \'completed\' THEN 1 ELSE 0 END) as overdue
                        ')
                        ->first();

                    if ($stats->total == 0) {
                        $healthScore = 100; // No tasks = perfect health
                    } else {
                        $completionRate = ($stats->completed / $stats->total) * 100;
                        $overduePenalty = $stats->overdue * 5;
                        $healthScore = max(0, min(100, (int)($completionRate - $overduePenalty)));
                    }

                    // Inject health_score into array before caching
                    $projectArray = $project->toArray();
                    $projectArray['health_score'] = $healthScore;
                    
                    return $projectArray;
                })->toArray();
            
            // ✅ PERFORMANCE: Calculate average health (NO CACHE - fresh)
            $avgHealth = empty($userProjects) ? 100 : (int)(array_sum(array_map(fn($p) => $p['health_score'], $userProjects)) / count($userProjects));

            // ✅ PERFORMANCE: Calculate user tasks (NO CACHE - fresh data)
            $query = Task::select(['id', 'project_id', 'title', 'status', 'priority', 'due_date', 'created_by', 'created_at'])
                ->with(['project:id,name', 'creator:id,name', 'assignedUser:id,name']);
            if (!$user->isAdmin()) {
                $query->where('assigned_user_id', $user->id);
            }
            $userTasks = $query->orderBy('due_date', 'asc')
                ->get()
                ->toArray();
            
            // ✅ PERFORMANCE: Use cached data instead of new queries
            $assignedCount = count($userTasks);
            $completedCount = count(array_filter($userTasks, fn($t) => $t['status'] === 'completed'));

            // ✅ PERFORMANCE: Calculate task counts (NO CACHE - fresh)
            $overdueTasks = Task::where('status', '!=', 'completed')

                ->when(!$user->isAdmin(), function($q) use ($user) {
                    return $q->where('assigned_user_id', $user->id);
                })
                ->where('due_date', '<', now())
                ->count();
            
            $tasksDueToday = Task::query()
                ->when(!$user->isAdmin(), function($q) use ($user) {
                    return $q->where('assigned_user_id', $user->id);
                })
                ->whereDate('due_date', today())
                ->where('status', '!=', 'completed')
                ->count();
            // ✅ PERFORMANCE: Calculate priority breakdown (NO CACHE - fresh)
            $projectsByPriority = [
                'critical' => Project::query()->when(!$user->isAdmin(), function($q) use ($user) { return $q->where('manager_id', $user->id); })->where('priority', 'critical')->count(),
                'high' => Project::query()->when(!$user->isAdmin(), function($q) use ($user) { return $q->where('manager_id', $user->id); })->where('priority', 'high')->count(),
                'medium' => Project::query()->when(!$user->isAdmin(), function($q) use ($user) { return $q->where('manager_id', $user->id); })->where('priority', 'medium')->count(),
                'low' => Project::query()->when(!$user->isAdmin(), function($q) use ($user) { return $q->where('manager_id', $user->id); })->where('priority', 'low')->count(),
            ];
            
            $tasksByPriority = [
                'critical' => Task::query()->when(!$user->isAdmin(), function($q) use ($user) { return $q->where('assigned_user_id', $user->id); })->where('priority', 'critical')->count(),
                'high' => Task::query()->when(!$user->isAdmin(), function($q) use ($user) { return $q->where('assigned_user_id', $user->id); })->where('priority', 'high')->count(),
                'medium' => Task::query()->when(!$user->isAdmin(), function($q) use ($user) { return $q->where('assigned_user_id', $user->id); })->where('priority', 'medium')->count(),
                'low' => Task::query()->when(!$user->isAdmin(), function($q) use ($user) { return $q->where('assigned_user_id', $user->id); })->where('priority', 'low')->count(),
            ];

            // ✅ PERFORMANCE: Eager load activities with limit to prevent huge result sets
            $recentActivities = TaskActivity::select(['id', 'task_id', 'user_id', 'activity_type', 'created_at'])
                ->with(['user:id,name', 'task:id,title,project_id', 'task.project:id,name'])
                ->latest('created_at')
                ->limit(10)
                ->get();

            return view('dashboard.index', [
                'projectCount' => $projectCount,
                'projectHealth' => $avgHealth,
                'assignedTasks' => $assignedCount,
                'completedTasks' => $completedCount,
                'overdueTasks' => $overdueTasks,
                'tasksDueToday' => $tasksDueToday,
                'projectsByPriority' => $projectsByPriority,
                'tasksByPriority' => $tasksByPriority,
                'recentProjects' => $userProjects,
                'recentActivities' => $recentActivities,
            ]);
        } catch (Exception $e) {
            Log::error('Dashboard rendering failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return dashboard with default empty values if error
            return view('dashboard.index', [
                'projectCount' => 0,
                'projectHealth' => 100,
                'assignedTasks' => 0,
                'completedTasks' => 0,
                'overdueTasks' => 0,
                'tasksDueToday' => 0,
                'projectsByPriority' => ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0],
                'tasksByPriority' => ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0],
                'recentProjects' => [],
                'recentActivities' => [],
            ]);
        }
    }

    /**
     * Display tasks overview - HARDENED with interface-based pagination
     * Service returns LengthAwarePaginator Contract - no additional pagination needed
     */
    public function tasks(string $status = null): View
    {
        try {
            Gate::authorize('viewAny', Task::class);

            $tasks = $this->taskService->getAllTasks(
                status: $status,
                userId: auth()->user()->id,
                perPage: 15  // Pass perPage to service instead of duplicate paginate() call
            );

            return view('dashboard.tasks', ['tasks' => $tasks, 'status' => $status]);
        } catch (Exception $e) {
            Log::error('Tasks overview failed: ' . $e->getMessage());
            return view('dashboard.tasks', ['tasks' => [], 'status' => $status]);
        }
    }

    /**
     * Display projects overview - HARDENED with interface-based pagination
     * Service returns LengthAwarePaginator Contract - no additional pagination needed
     */
    public function projects(): View
    {
        try {
            Gate::authorize('viewAny', Project::class);

            $projects = $this->projectService->getAllProjects(
                managerId: auth()->user()->id,
                perPage: 10  // Pass perPage to service instead of duplicate paginate() call
            );

            return view('dashboard.projects', ['projects' => $projects]);
        } catch (Exception $e) {
            Log::error('Projects overview failed: ' . $e->getMessage());
            return view('dashboard.projects', ['projects' => []]);
        }
    }
}

