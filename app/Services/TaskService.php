<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class TaskService
{
    /**
     * Get paginated tasks with optional filtering - OPTIMIZED with selective columns
     * 
     * @param string|null $status Filter by task status (pending, in_progress, completed)
     * @param int|null $projectId Filter tasks by project ID
     * @param int|null $userId Filter tasks assigned to specific user
     * @param int $perPage Items per page (default: 15) - requires COUNT(*) for LengthAwarePaginator
     * @return LengthAwarePaginator Contract-based paginator for flexible pagination engine swapping
     * @throws Exception On database query failure
     * 
     * @uses Illuminate\Contracts\Pagination\LengthAwarePaginator for Dependency Inversion Principle
     *       Using Contract allows pagination implementation to change without breaking contract
     */
    public function getAllTasks(?string $status = null, ?int $projectId = null, ?int $userId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Task::select(['id', 'project_id', 'title', 'status', 'priority', 'due_date', 'assigned_user_id', 'created_by', 'created_at'])
            ->with([
                'project:id,name,status',
                'assignedUser:id,name,email',
                'creator:id,name,email'
            ]);

        if ($status) {
            $query->where('status', $status);
        }

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        if ($userId) {
            $query->where('assigned_user_id', $userId);
        }

        return $query->latest('created_at')->paginate($perPage);
    }

    /**
     * Get high priority overdue tasks - OPTIMIZED with selective columns
     */
    public function getOverdueTasks(): array
    {
        return Task::select(['id', 'project_id', 'title', 'priority', 'due_date', 'assigned_user_id', 'status'])
            ->with([
                'project:id,name',
                'assignedUser:id,name'
            ])
            ->where('priority', 'high')
            ->where('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->orderBy('due_date', 'asc')
            ->limit(50)
            ->get()
            ->toArray();
    }

    /**
     * Get user's assigned tasks - OPTIMIZED with selective columns
     */
    public function getUserTasks(int $userId, ?string $status = null): array
    {
        $query = Task::select(['id', 'project_id', 'title', 'status', 'priority', 'due_date', 'created_by', 'created_at'])
            ->with([
                'project:id,name',
                'creator:id,name'
            ])
            ->where('assigned_user_id', $userId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('due_date', 'asc')->get()->toArray();
    }

    /**
     * Create a new task with ATOMIC TRANSACTION - rollback on Activity Log failure
     * ✅ HARDENED: Wrapped in DB::transaction()
     */
    public function createTask(array $data): Task
    {
        try {
            return DB::transaction(function () use ($data) {
                $task = Task::create($data);
                
                // If Task creation succeeds, event observers handle activity logging
                // If activity logging fails, the entire transaction is rolled back
                return $task->refresh();
            });
        } catch (Exception $e) {
            Log::error('Task creation failed: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing task - OPTIMIZED and HARDENED with transactions
     */
    public function updateTask(int $taskId, array $data): Task
    {
        try {
            return DB::transaction(function () use ($taskId, $data) {
                $task = Task::findOrFail($taskId);
                $task->update($data);
                return $task->refresh();
            });
        } catch (Exception $e) {
            Log::error('Task update failed for task ID: ' . $taskId, [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a task
     */
    public function deleteTask(int $taskId): bool
    {
        try {
            return DB::transaction(function () use ($taskId) {
                return Task::findOrFail($taskId)->delete();
            });
        } catch (Exception $e) {
            Log::error('Task deletion failed for task ID: ' . $taskId, [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get project statistics - OPTIMIZED with database aggregation
     */
    public function getProjectStats(int $projectId): array
    {
        try {
            $project = Project::select(['id'])->findOrFail($projectId);
            
            // Use database-level aggregation instead of PHP loops (way faster)
            $stats = Task::where('project_id', $projectId)
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = "in_progress" THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN priority = "high" THEN 1 ELSE 0 END) as high_priority,
                    SUM(CASE WHEN due_date < NOW() AND status != "completed" THEN 1 ELSE 0 END) as overdue
                ')
                ->first();

            return [
                'total' => (int) $stats->total,
                'completed' => (int) $stats->completed,
                'in_progress' => (int) $stats->in_progress,
                'pending' => (int) $stats->pending,
                'high_priority' => (int) $stats->high_priority,
                'overdue' => (int) $stats->overdue,
            ];
        } catch (Exception $e) {
            Log::error('Project stats retrieval failed for project ID: ' . $projectId, [
                'error' => $e->getMessage()
            ]);
            return [
                'total' => 0,
                'completed' => 0,
                'in_progress' => 0,
                'pending' => 0,
                'high_priority' => 0,
                'overdue' => 0,
            ];
        }
    }

    /**
     * Get tasks by priority - OPTIMIZED with selective columns
     */
    public function getTasksByPriority(string $priority): array
    {
        return Task::select(['id', 'project_id', 'title', 'status', 'priority', 'due_date', 'assigned_user_id'])
            ->with([
                'project:id,name',
                'assignedUser:id,name'
            ])
            ->where('priority', $priority)
            ->orderBy('due_date', 'asc')
            ->limit(50)
            ->get()
            ->toArray();
    }

    /**
     * Get tasks due today - OPTIMIZED with selective columns
     */
    public function getTasksDueToday(): array
    {
        return Task::select(['id', 'project_id', 'title', 'status', 'priority', 'due_date', 'assigned_user_id'])
            ->with([
                'project:id,name',
                'assignedUser:id,name'
            ])
            ->whereDate('due_date', today())
            ->where('status', '!=', 'completed')
            ->orderBy('priority')
            ->limit(50)
            ->get()
            ->toArray();
    }
}
