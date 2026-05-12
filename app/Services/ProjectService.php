<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class ProjectService
{
    /**
     * Get all projects with pagination and optional filtering - OPTIMIZED with selective columns
     * 
     * @param string|null $status Filter by project status (planning, active, on_hold, completed)
     * @param int|null $managerId Filter by manager ID - returns only projects managed by this user
     * @param int $perPage Items per page (default: 15) - requires COUNT(*) for LengthAwarePaginator
     * @return LengthAwarePaginator Contract-based paginator for flexible pagination engine swapping
     * @throws Exception On database query failure
     * 
     * @uses Illuminate\Contracts\Pagination\LengthAwarePaginator for Dependency Inversion Principle
     *       Using Contract allows pagination implementation to change without breaking contract
     */
    public function getAllProjects(?string $status = null, ?int $managerId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Project::select(['id', 'name', 'status', 'priority', 'due_date', 'manager_id', 'created_at'])
            ->with([
                'manager:id,name,email',
                'tasks:id,project_id,status,priority'
            ]);

        if ($status) {
            $query->where('status', $status);
        }

        if ($managerId) {
            $query->where('manager_id', $managerId);
        }

        return $query->latest('created_at')->paginate($perPage);
    }

    /**
     * Get projects managed by a specific user - OPTIMIZED with selective columns
     */
    public function getUserProjects(int $userId): array
    {
        return Project::select(['id', 'name', 'description', 'status', 'priority', 'due_date', 'manager_id', 'updated_at'])
            ->with(['tasks:id,project_id,status,priority'])
            ->where('manager_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Create a new project with ATOMIC TRANSACTION
     * ✅ HARDENED: Wrapped in DB::transaction() with comprehensive error diagnostics
     * 
     * @param array $data Project data with keys: name, description, status, priority, start_date, due_date, manager_id
     * @return Project The created project instance
     * @throws Exception If creation fails - exception is logged with full context
     */
    public function createProject(array $data): Project
    {
        try {
            // Validate required fields before transaction
            $required = ['name', 'due_date', 'status'];
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    throw new Exception("Required field missing: $field");
                }
            }

            return DB::transaction(function () use ($data) {
                try {
                    $project = Project::create($data);
                    // If Project creation succeeds, observers handle activity logging
                    // If anything fails, rollback entire transaction
                    return $project->refresh();
                } catch (\Exception $e) {
                    // Log transaction-level errors with full context
                    Log::error('Project creation transaction failed', [
                        'error_message' => $e->getMessage(),
                        'error_code' => $e->getCode(),
                        'data_keys' => array_keys($data),
                        'trace' => $e->getTraceAsString(),
                        'line' => $e->getLine(),
                    ]);
                    throw $e;
                }
            });
        } catch (Exception $e) {
            // Log outer-level transaction errors
            Log::error('Project creation failed', [
                'exception_class' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'data' => $data,
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toIso8601String(),
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing project - HARDENED with transactions
     */
    public function updateProject(int $projectId, array $data): Project
    {
        try {
            return DB::transaction(function () use ($projectId, $data) {
                $project = Project::findOrFail($projectId);
                $project->update($data);
                return $project->refresh();
            });
        } catch (Exception $e) {
            Log::error('Project update failed for project ID: ' . $projectId, [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a project
     */
    public function deleteProject(int $projectId): bool
    {
        try {
            return DB::transaction(function () use ($projectId) {
                return Project::findOrFail($projectId)->delete();
            });
        } catch (Exception $e) {
            Log::error('Project deletion failed for project ID: ' . $projectId, [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get active projects - OPTIMIZED with selective columns
     */
    public function getActiveProjects(): array
    {
        return Project::select(['id', 'name', 'description', 'status', 'manager_id', 'updated_at'])
            ->with(['manager:id,name', 'tasks:id,project_id,status'])
            ->where('status', 'active')
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get()
            ->toArray();
    }

    /**
     * Get overdue projects - OPTIMIZED with selective columns
     */
    public function getOverdueProjects(): array
    {
        return Project::select(['id', 'name', 'status', 'due_date', 'manager_id'])
            ->with(['manager:id,name', 'tasks:id,project_id,status'])
            ->where('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->orderBy('due_date', 'asc')
            ->limit(50)
            ->get()
            ->toArray();
    }

    /**
     * Get project health score (0-100) - OPTIMIZED with database aggregation
     * Based on task completion and timeline
     */
    public function getProjectHealth(int $projectId): int
    {
        try {
            $project = Project::select(['id'])->findOrFail($projectId);
            
            // Use database aggregation for speed
            $stats = DB::table('tasks')
                ->where('project_id', $projectId)
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = \'completed\' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN due_date < NOW() AND status != \'completed\' THEN 1 ELSE 0 END) as overdue
                ')
                ->first();

            if ($stats->total == 0) {
                return 100; // No tasks = perfect health
            }

            $completionRate = ($stats->completed / $stats->total) * 100;
            $healthScore = (int) $completionRate;

            // Reduce score for each overdue task (penalty system)
            $healthScore -= ($stats->overdue * 5);

            return max(0, min(100, $healthScore));
        } catch (Exception $e) {
            Log::error('Project health calculation failed for project ID: ' . $projectId, [
                'error' => $e->getMessage()
            ]);
            return 100; // Default to perfect if error
        }
    }
}
