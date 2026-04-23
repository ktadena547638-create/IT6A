<?php

namespace App\Observers;

use App\Events\ProjectMilestone;
use App\Models\Project;
use App\Models\TaskActivity;

class ProjectObserver
{
    /**
     * Handle the Project "created" event.
     * Log project creation activity for audit trail
     */
    public function created(Project $project): void
    {
        try {
            if (!auth()->check()) {
                return; // Skip if not authenticated
            }

            TaskActivity::create([
                'task_id' => null,  // Project-level activity has no associated task
                'user_id' => auth()->id(),
                'activity_type' => 'project_created',
                'description' => "Project '{$project->name}' was created",
                'metadata' => json_encode([
                    'project_id' => $project->id,
                    'status' => $project->status,
                    'due_date' => $project->due_date?->toDateString(),
                ]),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to log project creation activity', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);
            // Don't throw - activity logging is non-critical
        }
    }

    /**
     * Handle the Project "updated" event.
     * Log project update activity for audit trail + Dispatch milestone event if client assigned
     */
    public function updated(Project $project): void
    {
        try {
            if (!auth()->check()) {
                return; // Skip if not authenticated
            }

            $changes = $project->getChanges();
            $changedFields = array_keys($changes);

            if (empty($changedFields)) {
                return; // No actual changes
            }

            TaskActivity::create([
                'task_id' => null,  // Project-level activity has no associated task
                'user_id' => auth()->id(),
                'activity_type' => 'project_updated',
                'description' => "Project '{$project->name}' was updated. Changed: " . implode(', ', $changedFields),
                'metadata' => json_encode([
                    'project_id' => $project->id,
                    'changed_fields' => $changedFields,
                ]),
            ]);

            // ✅ PROJECT MILESTONE: Dispatch event if client was just assigned
            if ($project->wasChanged('client_id') && $project->client_id) {
                ProjectMilestone::dispatch($project);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to log project update activity', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);
            // Don't throw - activity logging is non-critical
        }
    }

    /**
     * Handle the Project "deleted" event.
     * Log project deletion activity for audit trail
     */
    public function deleted(Project $project): void
    {
        try {
            if (!auth()->check()) {
                return; // Skip if not authenticated
            }

            TaskActivity::create([
                'task_id' => null,  // Project-level activity has no associated task
                'user_id' => auth()->id(),
                'activity_type' => 'project_deleted',
                'description' => "Project '{$project->name}' was deleted",
                'metadata' => json_encode([
                    'project_id' => $project->id,
                ]),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to log project deletion activity', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);
            // Don't throw - activity logging is non-critical
        }
    }
}
