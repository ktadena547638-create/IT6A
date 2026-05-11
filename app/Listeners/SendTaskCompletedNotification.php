<?php

namespace App\Listeners;

use App\Events\TaskUpdated;
use App\Notifications\TaskFlowNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Exception;

class SendTaskCompletedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * ✅ COMPLETION MILESTONE: Notify manager when team member completes task
     * Triggers: TaskUpdated event when a task status changes to 'completed'
     * 
     * FIXED: Added try-catch wrapper + null checks for relationships
     * FIXED: Verify task->wasChanged('status') is safe with $task->getOriginal() fallback
     * FIXED: Null-check assignedUser and project before accessing
     * Before: Could throw AttributeNotFoundException if relationships not loaded
     * After: Gracefully handles missing relationships + logs errors
     */
    public function handle(TaskUpdated $event): void
    {
        try {
            $task = $event->task;

            // ✅ DEFENSIVE: Check that task status actually changed to completed
            $statusChanged = $task->wasChanged('status') 
                ? $task->wasChanged('status') 
                : (isset($task->getOriginal()['status']) && $task->getOriginal()['status'] !== 'completed' && $task->status === 'completed');

            // Only send notification if task was just completed AND task is actually in completed state
            if ($task->status === 'completed' && $statusChanged) {
                // ✅ DEFENSIVE: Ensure relationships are loaded before accessing
                if (!$task->relationLoaded('project')) {
                    $task->load('project');
                }
                if (!$task->relationLoaded('assignedUser')) {
                    $task->load('assignedUser');
                }

                // ✅ NULL-SAFE: Check project exists
                if (!$task->project) {
                    Log::warning('Task completed notification: Project not found', ['task_id' => $task->id]);
                    return;
                }

                // ✅ NULL-SAFE: Check manager exists
                if (!$task->project->manager) {
                    Log::warning('Task completed notification: Project manager not found', ['task_id' => $task->id, 'project_id' => $task->project->id]);
                    return;
                }

                // ✅ SAFE: Use optional chaining for assignedUser
                $assignedUserName = optional($task->assignedUser)->name ?? 'Someone';

                // Send notification to manager
                $task->project->manager->notify(
                    new TaskFlowNotification(
                        title: '✅ Task Completed',
                        message: "{$assignedUserName} completed: {$task->title}",
                        actionUrl: route('tasks.show', $task),
                        actionLabel: 'View Task'
                    )
                );

                Log::info('Task completion notification sent', [
                    'task_id' => $task->id,
                    'manager_id' => $task->project->manager->id,
                    'timestamp' => now()
                ]);
            }
        } catch (Exception $e) {
            // ✅ HARDENED: Log all exceptions without crashing
            Log::error('Failed to send task completed notification', [
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
