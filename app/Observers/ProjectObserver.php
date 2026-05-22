<?php

namespace App\Observers;

use App\Events\ProjectMilestone;
use App\Models\Project;
use Illuminate\Support\Facades\Log;

class ProjectObserver
{
    /**
     * Handle the Project "created" event.
     * Log project creation activity for audit trail
     */
    public function created(Project $project): void
    {
        Log::info('Project created', [
            'project_id' => $project->id,
            'name' => $project->name,
            'status' => $project->status,
            'manager_id' => $project->manager_id,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Handle the Project "updated" event.
     * Log project update activity for audit trail + Dispatch milestone event if client assigned
     */
    public function updated(Project $project): void
    {
        try {
            $changes = $project->getChanges();
            $changedFields = array_keys($changes);

            if (empty($changedFields)) {
                return;
            }

            Log::info('Project updated', [
                'project_id' => $project->id,
                'name' => $project->name,
                'changed_fields' => $changedFields,
                'updated_by' => auth()->id(),
            ]);

            if ($project->wasChanged('client_id') && $project->client_id) {
                ProjectMilestone::dispatch($project);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to log project update activity', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Project "deleted" event.
     * Log project deletion activity for audit trail
     */
    public function deleted(Project $project): void
    {
        Log::info('Project deleted', [
            'project_id' => $project->id,
            'name' => $project->name,
            'deleted_by' => auth()->id(),
        ]);
    }
}

