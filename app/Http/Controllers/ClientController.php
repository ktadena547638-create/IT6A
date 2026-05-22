<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Client Dashboard - Shows projects owned by this client
     * ✅ SELECTIVE SCRYING: Clients can only see their own projects
     */
    public function dashboard(): View
    {
        try {
            // Ensure user is a client
            if (!Auth::user()->isClient()) {
                abort(403, 'Unauthorized: Client access required');
            }

            // Eager load relationships to avoid N+1 queries
            $projects = Auth::user()->clientProjects()
                ->with(['manager:id,name', 'tasks:id,project_id,status,assigned_user_id'])
                ->withCount('tasks')
                ->get();

            // Calculate project health metrics
            $projectsData = $projects->map(function ($project) {
                $totalTasks = $project->tasks_count;
                $completedTasks = $project->tasks()->where('status', 'completed')->count();
                $activeTasks = $project->tasks()->whereIn('status', ['pending', 'in_progress'])->count();
                $completionPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

                return [
                    'project' => $project,
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'active_tasks' => $activeTasks,
                    'completion_percentage' => $completionPercentage,
                ];
            });

            return view('client.dashboard', compact('projectsData'));
        } catch (Exception $e) {
            Log::error('Failed to load client dashboard', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return view('client.dashboard', ['projectsData' => collect()]);
        }
    }

    /**
     * View a single project (read-only for clients)
     * ✅ SELECTIVE SCRYING: Clients can only view their own projects
     */
    public function viewProject(Project $project): View|RedirectResponse
    {
        try {
            // Verify this project belongs to the current client
            if ($project->client_id !== Auth::id()) {
                abort(403, 'Unauthorized: You can only view your own projects');
            }

            // Eager load relationships
            $project->load(['manager:id,name', 'tasks.assignedUser:id,name']);

            // Calculate project health
            $totalTasks = $project->tasks()->count();
            $completedTasks = $project->tasks()->where('status', 'completed')->count();
            $activeTasks = $project->tasks()->whereIn('status', ['pending', 'in_progress'])->count();
            $completionPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

            return view('client.project-view', compact('project', 'totalTasks', 'completedTasks', 'activeTasks', 'completionPercentage'));
        } catch (Exception $e) {
            Log::error('Failed to load client project view', ['project_id' => $project->id, 'user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return redirect()->route('client.dashboard')->with('error', 'Failed to load project');
        }
    }

    /**
     * View tasks for a project (read-only for clients)
     * ✅ SELECTIVE SCRYING: Clients can only see tasks in their own projects
     */
    public function viewProjectTasks(Project $project): View|RedirectResponse
    {
        try {
            // Verify this project belongs to the current client
            if ($project->client_id !== Auth::id()) {
                abort(403, 'Unauthorized: You can only view your own projects');
            }

            // Eager load tasks and assignees
            $tasks = $project->tasks()
                ->with(['assignedUser:id,name', 'project:id,name'])
                ->get();

            // Group tasks by status
            $tasksByStatus = $tasks->groupBy('status');

            return view('client.project-tasks', compact('project', 'tasksByStatus'));
        } catch (Exception $e) {
            Log::error('Failed to load client project tasks', ['project_id' => $project->id, 'user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return redirect()->route('client.dashboard')->with('error', 'Failed to load project tasks');
        }
    }
}

