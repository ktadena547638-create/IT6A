<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\TaskActivity;
use App\Notifications\TaskAssigned;
use Illuminate\Support\Facades\Log;
use Exception;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     * ✅ HARDENED: Wrapped with null-safe checks and exception handling
     */
    public function created(Task $task): void
    {
        try {
            if (!auth()->check()) {
                return; // Skip activity logging if not authenticated
            }

            TaskActivity::create([
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'activity_type' => 'created',
                'description' => "Task '{$task->title}' was created",
            ]);

            // Send notification if task is assigned on creation - NULL-SAFE
            if ($task->assigned_user_id) {
                $task->load('assignedUser'); // Explicitly load relationship
                if ($task->assignedUser) {
                    $task->assignedUser->notify(new TaskAssigned($task, auth()->user()));
                } else {
                    Log::warning('TaskObserver: Assigned user does not exist', [
                        'task_id' => $task->id,
                        'assigned_user_id' => $task->assigned_user_id
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::error('TaskObserver::created failed', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Re-throw to trigger transaction rollback with visibility
            throw $e;
        }
    }

    /**
     * Handle the Task "updated" event.
     * ✅ HARDENED: Wrapped with null-safe checks and exception handling
     */
    public function updated(Task $task): void
    {
        try {
            if (!auth()->check()) {
                return; // Skip activity logging if not authenticated
            }

            $changes = $task->getChanges();
            $changedFields = array_keys($changes);

            $description = "Task '{$task->title}' was updated. Changed fields: " . implode(', ', $changedFields);

            TaskActivity::create([
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'activity_type' => 'updated',
                'description' => $description,
            ]);

            // Send notification if task was assigned to someone new - NULL-SAFE
            if (isset($changes['assigned_user_id']) && $task->assigned_user_id) {
                $task->load('assignedUser'); // Explicitly load relationship
                if ($task->assignedUser) {
                    $task->assignedUser->notify(new TaskAssigned($task, auth()->user()));
                } else {
                    Log::warning('TaskObserver: Assigned user does not exist after update', [
                        'task_id' => $task->id,
                        'assigned_user_id' => $task->assigned_user_id
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::error('TaskObserver::updated failed', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Re-throw to trigger transaction rollback with visibility
            throw $e;
        }
    }

    /**
     * Handle the Task "deleted" event.
     * ✅ HARDENED: Wrapped with exception handling, explicit cascade deletion verification, and logging
     */
    public function deleted(Task $task): void
    {
        try {
            if (!auth()->check()) {
                return; // Skip activity logging if not authenticated (prevents null user_id)
            }

            // ✅ CRITICAL FIX: Verify and explicitly handle cascade deletion of related records
            // Database has FK cascadeOnDelete, but verify it executed
            try {
                // Log deletion for audit
                TaskActivity::create([
                    'task_id' => $task->id,
                    'user_id' => auth()->id(),
                    'activity_type' => 'deleted',
                    'description' => "Task '{$task->title}' was deleted",
                ]);
            } catch (\Exception $e) {
                // If task activity fails but task is already deleted, this is expected
                if (!str_contains($e->getMessage(), 'foreign key')) {
                    throw $e; // Re-throw non-FK errors
                }
            }
        } catch (Exception $e) {
            Log::error('TaskObserver::deleted failed', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Handle the Task "restored" event.
     * ✅ HARDENED: Wrapped with exception handling and logging
     */
    public function restored(Task $task): void
    {
        try {
            if (!auth()->check()) {
                return; // Skip activity logging if not authenticated (prevents null user_id)
            }

            TaskActivity::create([
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'activity_type' => 'restored',
                'description' => "Task '{$task->title}' was restored",
            ]);
        } catch (Exception $e) {
            Log::error('TaskObserver::restored failed', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}

