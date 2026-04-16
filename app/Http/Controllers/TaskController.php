<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService) {}

    /**
     * Display a listing of tasks with dynamic filtering
     * ✅ OPTIMIZED: Search, Status, Priority filters + Eager loading (no N+1)
     * ✅ SOVEREIGN: Admins see all; Managers see their projects/tasks
     * ✅ CLEAN: Uses ->when() blocks for conditional filtering
     */
    public function index(Request $request): View
    {
        try {
            Gate::authorize('viewAny', Task::class);
            
            $search = $request->get('search', '');
            $status = $request->get('status', '');
            $priority = $request->get('priority', '');
            
            // Build query with eager loading
            $query = Task::with(['project', 'assignedUser', 'creator'])
                ->orderBy('created_at', 'desc');
            
            // Scope: Admins see all; Managers see only their projects' tasks
            if (!auth()->user()->isAdmin()) {
                $query->where(function ($q) {
                    $q->where('created_by', auth()->id())
                      ->orWhere('assigned_user_id', auth()->id())
                      ->orWhereHas('project', function ($subQ) {
                          $subQ->where('manager_id', auth()->id());
                      });
                });
            }
            
            // Apply filters conditionally with ->when()
            $tasks = $query
                ->when($search, function ($q) use ($search) {
                    return $q->where(function ($subQ) use ($search) {
                        $subQ->where('title', 'LIKE', "%{$search}%")
                             ->orWhere('description', 'LIKE', "%{$search}%");
                    });
                })
                ->when($status, function ($q) use ($status) {
                    return $q->where('status', $status);
                })
                ->when($priority, function ($q) use ($priority) {
                    return $q->where('priority', $priority);
                })
                ->paginate(15);
            
            return view('tasks.index', compact('tasks', 'search', 'status', 'priority'));
        } catch (Exception $e) {
            Log::error('Failed to retrieve tasks list', ['error' => $e->getMessage()]);
            return view('tasks.index', ['tasks' => collect(), 'search' => '', 'status' => '', 'priority' => '']);
        }
    }

    /**
     * Show the form for creating a new task
     */
    public function create(): View
    {
        try {
            Gate::authorize('create', Task::class);
            // Admins see all projects; non-admins see only their managed projects
            $projectsQuery = auth()->user()->isAdmin() 
                ? Project::select(['id', 'name', 'manager_id'])
                : Project::where('manager_id', auth()->id())->select(['id', 'name', 'manager_id']);
            $projects = $projectsQuery->get();
            // Users available for assignment (Admins can assign to anyone)
            $users = auth()->user()->isAdmin() 
                ? User::select(['id', 'name'])->get()
                : User::whereIn('role', ['user', 'project_manager'])->select(['id', 'name'])->get();
            return view('tasks.create', compact('projects', 'users'));
        } catch (Exception $e) {
            Log::error('Failed to load task creation form', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return view('tasks.create', ['projects' => [], 'users' => []]);
        }
    }

    /**
     * Store a newly created task
     * ✅ HARDENED: Wrapped with granular error handling and logging
     * Failure messages provide high-signal feedback (validation vs system errors)
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        try {
            Gate::authorize('create', Task::class);

            $task = $this->taskService->createTask(array_merge(
                $request->validated(),
                ['created_by' => auth()->id()]
            ));

            return redirect()->route('tasks.show', $task)->with('success', 'Task created successfully.');
        } catch (QueryException $e) {
            // Database constraint violation (FK, unique, null, etc.)
            Log::error('Task creation database error', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'sql_state' => $e->getCode(),
                'data' => $request->validated()
            ]);
            return redirect()->back()->withInput()->with('error', 'Associated project or user does not exist or constraint violation. Check your inputs.');
        } catch (\Exception $e) {
            // Generic or unhandled exceptions
            Log::error('Task creation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'data' => $request->validated()
            ]);
            return redirect()->back()->withInput()->with('error', 'Failed to create task. Please try again or contact support.');
        }
    }

    /**
     * Display the specified task
     */
    public function show(Task $task): View
    {
        try {
            // Preload project relationship for authorization check
            if (!$task->relationLoaded('project')) {
                $task->load('project');
            }
            
            Gate::authorize('view', $task);
            $task->load(['assignedUser', 'creator', 'comments.user', 'activities']);
            return view('tasks.show', compact('task'));
        } catch (Exception $e) {
            Log::error('Failed to retrieve task', ['task_id' => $task->id, 'error' => $e->getMessage()]);
            abort(500, 'Failed to load task');
        }
    }

    /**
     * Show the form for editing the task
     */
    public function edit(Task $task): View
    {
        try {
            Gate::authorize('update', $task);
            // Admins see all projects; non-admins see only their managed projects
            $projectsQuery = auth()->user()->isAdmin() 
                ? Project::select(['id', 'name', 'manager_id'])
                : Project::where('manager_id', auth()->id())->select(['id', 'name', 'manager_id']);
            $projects = $projectsQuery->get();
            return view('tasks.edit', compact('task', 'projects'));
        } catch (Exception $e) {
            Log::error('Failed to load task edit form', ['task_id' => $task->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to load task for editing');
        }
    }

    /**
     * Update the specified task
     * ✅ HARDENED: DB::transaction() wrapper + granular error handling
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse | JsonResponse
    {
        try {
            Gate::authorize('update', $task);
            
            DB::transaction(function () use ($request, $task) {
                $this->taskService->updateTask($task->id, $request->validated());
            });

            // Support both form submission and AJAX requests
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Task updated successfully.']);
            }

            return redirect()->route('tasks.show', $task)->with('success', 'Task updated successfully.');
        } catch (QueryException $e) {
            // Database constraint violation (FK, unique, null, etc.)
            Log::error('Task update database error', [
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'sql_state' => $e->getCode(),
                'data' => $request->validated()
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Associated project or user does not exist. Check your inputs.'], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Associated project or user does not exist. Check your inputs.');
        } catch (Exception $e) {
            Log::error('Task update failed', [
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'data' => $request->validated()
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to update task'], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Failed to update task. Please try again or contact support.');
        }
    }

    /**
     * Remove the specified task
     * ✅ HARDENED: DB::transaction() wrapper + error handling
     */
    public function destroy(Task $task): RedirectResponse
    {
        try {
            Gate::authorize('delete', $task);
            
            DB::transaction(function () use ($task) {
                $this->taskService->deleteTask($task->id);
            });
            
            return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
        } catch (QueryException $e) {
            Log::error('Task deletion database error', [
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'sql_state' => $e->getCode()
            ]);
            return redirect()->back()->withErrors(['error' => 'Cannot delete task. It may have related records.']);
        } catch (Exception $e) {
            Log::error('Task deletion failed', [
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to delete task. Please try again or contact support.');
        }
    }

    /**
     * Mark a task as completed
     */
    public function complete(Task $task): RedirectResponse
    {
        try {
            Gate::authorize('complete', $task);
            $this->taskService->updateTask($task->id, ['status' => 'completed']);
            return redirect()->back()->with('success', 'Task marked as completed.');
        } catch (Exception $e) {
            Log::error('Task completion failed', ['task_id' => $task->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to mark task as completed.');
        }
    }

    /**
     * Get tasks by priority
     */
    public function byPriority(string $priority): View
    {
        try {
            Gate::authorize('viewAny', Task::class);

            if (!in_array($priority, ['high', 'medium', 'low', 'critical'])) {
                abort(404);
            }

            $tasks = $this->taskService->getTasksByPriority($priority);
            return view('tasks.priority', ['tasks' => $tasks, 'priority' => $priority]);
        } catch (Exception $e) {
            Log::error('Failed to retrieve tasks by priority', ['priority' => $priority, 'error' => $e->getMessage()]);
            return view('tasks.priority', ['tasks' => [], 'priority' => $priority]);
        }
    }

    /**
     * Get overdue tasks
     */
    public function overdue(): View
    {
        try {
            Gate::authorize('viewAny', Task::class);
            $tasks = $this->taskService->getOverdueTasks();
            return view('tasks.overdue', compact('tasks'));
        } catch (Exception $e) {
            Log::error('Failed to retrieve overdue tasks', ['error' => $e->getMessage()]);
            return view('tasks.overdue', ['tasks' => []]);
        }
    }
}
