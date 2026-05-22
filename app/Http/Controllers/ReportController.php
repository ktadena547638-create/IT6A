<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use App\Services\ProjectService;
use Illuminate\View\View;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    public function __construct(
        private TaskService $taskService,
        private ProjectService $projectService,
    ) {}

    /**
     * Display task reports and analytics
     * ✅ HARDENED: Case-normalized keys + null-safe defaults
     */
    public function tasks(): View
    {
        // Only admins can view reports
        if (!auth()->user()?->isAdmin()) {
            abort(403, 'Unauthorized to view reports.');
        }

        // Initialize all priority keys with defaults
        $tasksByPriority = [
            'critical' => 0,
            'high' => 0,
            'medium' => 0,
            'low' => 0,
        ];

        // Single aggregating query with lowercase normalization
        $taskCounts = \App\Models\Task::selectRaw('LOWER(priority) as priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority');
        
        // Merge with defaults (overwrite only if key exists in results)
        foreach (['critical', 'high', 'medium', 'low'] as $priority) {
            $tasksByPriority[$priority] = $taskCounts->get($priority, 0);
        }

        $overdueTasks = \App\Models\Task::where('status', '!=', 'completed')
            ->where('due_date', '<', now())
            ->count();
        
        $tasksDueToday = \App\Models\Task::whereDate('due_date', today())
            ->where('status', '!=', 'completed')
            ->count();

        return view('reports.tasks', compact('tasksByPriority', 'overdueTasks', 'tasksDueToday'));
    }

    /**
     * Display project health and performance reports
     */
    public function projects(): View
    {
        // Only admins can view reports
        if (!auth()->user()?->isAdmin()) {
            abort(403, 'Unauthorized to view reports.');
        }

        // Get all projects with proper collection handling
        $projects = \App\Models\Project::select(['id', 'name', 'status', 'created_at'])
            ->get()
            ->toArray();

        $healthScores = collect($projects)->map(fn ($p) => [
            'name' => $p['name'],
            'health' => $this->projectService->getProjectHealth($p['id']),
        ])->toArray();

        $avgHealth = collect($healthScores)->avg('health') ?? 0;

        return view('reports.projects', compact('healthScores', 'avgHealth', 'projects'));
    }

    /**
     * Display activity and audit logs
     */
    public function activities(): View
    {
        // Only admins can view reports
        if (!auth()->user()?->isAdmin()) {
            abort(403, 'Unauthorized to view reports.');
        }

        $activities = \App\Models\TaskActivity::with(['task', 'user'])
            ->latest()
            ->paginate(20);

        return view('reports.activities', compact('activities'));
    }
}

