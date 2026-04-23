<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class CalculateCapacityHeatmap implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $projectId;

    public function __construct($projectId = null)
    {
        $this->projectId = $projectId;
    }

    /**
     * Calculate and cache capacity heatmap for projects
     * Pre-calculation ensures <250ms response time
     */
    public function handle(): void
    {
        $projects = $this->projectId
            ? Project::find([$this->projectId])
            : Project::all();

        foreach ($projects as $project) {
            $heatmap = $this->buildHeatmap($project);
            Cache::put("capacity_heatmap:{$project->id}", $heatmap, now()->addHours(6));
        }
    }

    /**
     * Build capacity heatmap for a project
     */
    private function buildHeatmap(Project $project): array
    {
        $tasks = Task::where('project_id', $project->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->with('assignedUser')
            ->get();

        $heatmap = [];

        foreach ($tasks->groupBy('assigned_user_id') as $userId => $userTasks) {
            $totalHours = $userTasks->sum('estimated_hours');
            $taskCount = $userTasks->count();

            // Health indicators: Green <40, Yellow 40-70, Red >70
            $healthLevel = match (true) {
                $totalHours < 40 => 'green',
                $totalHours < 70 => 'yellow',
                default => 'red',
            };

            $heatmap[$userId] = [
                'total_hours' => $totalHours,
                'task_count' => $taskCount,
                'health' => $healthLevel,
                'percentage' => min(100, ($totalHours / 80) * 100),
            ];
        }

        return $heatmap;
    }
}
