<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Show analytics dashboard
     */
    public function index(): View
    {
        // Restrict to admins and project managers only
        if (!auth()->user()->isAdmin() && !auth()->user()->isProjectManager()) {
            abort(403, 'Unauthorized access to analytics');
        }

        return view('admin.analytics', [
            'stats' => $this->getStats(),
            'taskStats' => $this->getTaskStats(),
            'completionTrend' => $this->getCompletionTrend(),
        ]);
    }

    /**
     * Get overall statistics
     * ✅ HARDENED: Wrapped in caching to prevent N+1 query regression
     */
    private function getStats(): array
    {
        return \Illuminate\Support\Facades\Cache::remember(
            'analytics_stats_' . auth()->id(),
            now()->addMinutes(15),
            function () {
                $totalTasks = Task::count();
                $completedTasks = Task::where('status', 'completed')->count();
                $overdueTasks = Task::where('due_date', '<', now())
                    ->where('status', '!=', 'completed')
                    ->count();
                $inProgressTasks = Task::where('status', 'in_progress')->count();

                return [
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
                    'overdue_tasks' => $overdueTasks,
                    'in_progress_tasks' => $inProgressTasks,
                    'total_projects' => Project::count(),
                    'active_projects' => Project::where('status', 'active')->count(),
                ];
            }
        );
    }

    /**
     * Get task status distribution for pie chart
     * ✅ HARDENED: Wrapped in caching to prevent repeated counts
     */
    private function getTaskStats(): array
    {
        return \Illuminate\Support\Facades\Cache::remember(
            'analytics_task_stats_' . auth()->id(),
            now()->addMinutes(15),
            function () {
                $statuses = ['pending', 'in_progress', 'completed', 'on_hold', 'cancelled'];
                $data = [];
                $colors = [
                    'pending' => '#EF4444',
                    'in_progress' => '#F59E0B',
                    'completed' => '#10B981',
                    'on_hold' => '#6366F1',
                    'cancelled' => '#6B7280',
                ];

                foreach ($statuses as $status) {
                    $count = Task::where('status', $status)->count();
                    if ($count > 0) {
                        $data[] = [
                            'label' => ucfirst(str_replace('_', ' ', $status)),
                            'value' => $count,
                            'color' => $colors[$status],
                        ];
                    }
                }

                return $data;
            }
        );
    }

    /**
     * Get completion trend for the last 7 days
     * ✅ HARDENED: Wrapped in caching to prevent date-loop queries
     */
    private function getCompletionTrend(): array
    {
        return \Illuminate\Support\Facades\Cache::remember(
            'analytics_completion_trend_' . auth()->id(),
            now()->addMinutes(30),  // Longer TTL for historical data
            function () {
                $labels = [];
                $completed = [];
                $created = [];

                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $labels[] = $date->format('M d');

                    $completedCount = Task::where('status', 'completed')
                        ->whereDate('updated_at', $date)
                        ->count();
                    $completed[] = $completedCount;

                    $createdCount = Task::whereDate('created_at', $date)->count();
                    $created[] = $createdCount;
                }

                return [
                    'labels' => $labels,
                    'completed' => $completed,
                    'created' => $created,
                ];
            }
        );
    }

    /**
     * API endpoint for task status data (for AJAX)
     */
    public function taskStatusData(): JsonResponse
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isProjectManager()) {
            abort(403);
        }

        return response()->json($this->getTaskStats());
    }

    /**
     * API endpoint for completion trend data (for AJAX)
     */
    public function completionTrendData(): JsonResponse
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isProjectManager()) {
            abort(403);
        }

        return response()->json($this->getCompletionTrend());
    }
}

