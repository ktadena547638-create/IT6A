<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Notifications\TaskAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTaskAssignedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * ✅ ASSIGNMENT ALERT: Notify assignee when task is created and assigned
     * Triggers: TaskCreated event when a new task is created with an assigned_user_id
     */
    public function handle(TaskCreated $event): void
    {
        $task = $event->task;

        // Only send notification if task has an assigned user
        if ($task->assigned_user_id) {
            $task->assignedUser->notify(
                new TaskAssigned(
                    task: $task,
                    assignedBy: auth()->user() ?? $task->project->manager
                )
            );
        }
    }
}
