<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Notifications\TaskAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Exception;

class SendTaskAssignedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * ✅ ASSIGNMENT ALERT: Notify assignee when task is created and assigned
     * Triggers: TaskCreated event when a new task is created with an assigned_user_id
     * 
     * FIXED: Added try-catch wrapper + null checks + relationship loading
     * FIXED: Verify assignedUser exists before trying to notify
     * Before: Could throw exception if relationship not loaded or user not found
     * After: Gracefully skips notification if user doesn't exist + logs warning
     */
    public function handle(TaskCreated $event): void
    {
        try {
            $task = $event->task;

            // ✅ NULL-SAFE: Only proceed if task has an assigned user
            if (!$task->assigned_user_id) {
                Log::debug('Task created without assignment', ['task_id' => $task->id]);
                return;
            }

            // ✅ DEFENSIVE: Ensure assignedUser relationship is loaded
            if (!$task->relationLoaded('assignedUser')) {
                $task->load('assignedUser');
            }

            // ✅ NULL-SAFE: Verify assigned user exists in database
            if (!$task->assignedUser) {
                Log::warning('Task assigned to non-existent user', [
                    'task_id' => $task->id,
                    'assigned_user_id' => $task->assigned_user_id
                ]);
                return;
            }

            // ✅ DEFENSIVE: Load project relationship for notification context
            if (!$task->relationLoaded('project')) {
                $task->load('project');
            }

            if (!$task->project) {
                Log::warning('Task created with non-existent project', [
                    'task_id' => $task->id,
                    'project_id' => $task->project_id
                ]);
                return;
            }

            // ✅ SAFE: Determine who assigned the task
            $assignedBy = auth()->user() ?? optional($task->project)->manager;

            // Send notification to assigned user
            $task->assignedUser->notify(
                new TaskAssigned(
                    task: $task,
                    assignedBy: $assignedBy
                )
            );

            Log::info('Task assignment notification sent', [
                'task_id' => $task->id,
                'assigned_to_user_id' => $task->assignedUser->id,
                'timestamp' => now()
            ]);
        } catch (Exception $e) {
            // ✅ HARDENED: Log all exceptions without crashing listener
            Log::error('Failed to send task assignment notification', [
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'event_data' => isset($event) ? $event->task->toArray() : []
            ]);

            // Re-throw for queue retry mechanism
            throw $e;
        }
    }
}
