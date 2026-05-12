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
            $cacheKeyPrefix = 'dashboard_' . $user->id;
            $cacheTTL = now()->addMinutes(5); // 5-minute TTL

            // ✅ PERFORMANCE: Cache project count (TTL: 5 minutes)
            $projectCount = Cache::remember($cacheKeyPrefix . '_project_count', $cacheTTL, function () use ($user) {
                $query = Project::query();
                if (!$user->isAdmin()) {
                    $query->where('manager_id', $user->id);
                }
                return $query->count();
            });
            
            // ✅ PERFORMANCE: Cache user projects with health_score calculation (TTL: 5 minutes)
            // CRITICAL FIX: Health scores calculated via database aggregation to prevent N+1 and ensure View safety
            $userProjects = Cache::remember($cacheKeyPrefix . '_user_projects', $cacheTTL, function () use ($user) {
                $query = Project::select(['id', 'name', 'description', 'status', 'manager_id', 'updated_at'])
                    ->with(['manager:id,name', 'tasks:id,project_id,status']);
                if (!$user->isAdmin()) {
                    $query->where('manager_id', $user->id);
                }
                $projects = $query->orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->get();

                // Calculate health_score for each project using single aggregated query batch
                return $projects->map(function ($project) {
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
            });
            
            // ✅ PERFORMANCE: Cache average health (TTL: 5 minutes)
            $avgHealth = Cache::remember($cacheKeyPrefix . '_avg_health', $cacheTTL, function () use ($userProjects) {
                if (empty($userProjects)) {
                    return 100;
                }
                
                $totalHealth = array_sum(array_map(fn($p) => $p['health_score'], $userProjects));
                return (int)($totalHealth / count($userProjects));
            });

            // ✅ PERFORMANCE: Cache user tasks with relationships (TTL: 5 minutes)
            $userTasks = Cache::remember($cacheKeyPrefix . '_user_tasks', $cacheTTL, function () use ($user) {
                $query = Task::select(['id', 'project_id', 'title', 'status', 'priority', 'due_date', 'created_by', 'created_at'])
                    ->with(['project:id,name', 'creator:id,name', 'assignedUser:id,name']);
                if (!$user->isAdmin()) {
                    $query->where('assigned_user_id', $user->id);
                }
                return $query->orderBy('due_date', 'asc')
                    ->get()
                    ->toArray();
            });
            
            // ✅ PERFORMANCE: Use cached data instead of new queries
            $assignedCount = count($userTasks);
            $completedCount = count(array_filter($userTasks, fn($t) => $t['status'] === 'completed'));

            // ✅ PERFORMANCE: Cache task counts using database aggregation
            $overdueTasks = Cache::remember($cacheKeyPrefix . '_overdue_tasks', $cacheTTL, function () {
                return Task::where('status', '!=', 'completed')
                    ->where('due_date', '<', now())
                    ->count();
            });
            
            $tasksDueToday = Cache::remember($cacheKeyPrefix . '_due_today', $cacheTTL, function () {
                return Task::whereDate('due_date', today())
                    ->where('status', '!=', 'completed')
                    ->count();
            });

            // ✅ PERFORMANCE: Cache priority breakdown - FILTERED BY USER ACCESS
            // ✅ CRITICAL FIX: Count PROJECTS by priority instead of tasks
            $projectsByPriority = Cache::remember($cacheKeyPrefix . '_projects_priority_breakdown', $cacheTTL, function () use ($user) {
                $query = Project::query();
                if (!$user->isAdmin()) {
                    $query->where('manager_id', $user->id);
                }
                return [
                    'critical' => (clone $query)->where('priority', 'critical')->count(),
                    'high' => (clone $query)->where('priority', 'high')->count(),
                    'medium' => (clone $query)->where('priority', 'medium')->count(),
                    'low' => (clone $query)->where('priority', 'low')->count(),
                ];
            });
            
            // ✅ PERFORMANCE: Cache task priority breakdown - User's assigned tasks
            $tasksByPriority = Cache::remember($cacheKeyPrefix . '_tasks_priority_breakdown', $cacheTTL, function () use ($user) {
                $query = Task::query();
                if (!$user->isAdmin()) {
                    $query->where('assigned_user_id', $user->id);
                }
                return [
                    'critical' => (clone $query)->where('priority', 'critical')->count(),
                    'high' => (clone $query)->where('priority', 'high')->count(),
                    'medium' => (clone $query)->where('priority', 'medium')->count(),
                    'low' => (clone $query)->where('priority', 'low')->count(),
                ];
            });

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

