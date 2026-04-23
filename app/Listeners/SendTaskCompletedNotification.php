<?php

namespace App\Listeners;

use App\Events\TaskUpdated;
use App\Notifications\TaskFlowNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTaskCompletedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * ✅ COMPLETION MILESTONE: Notify manager when team member completes task
     * Triggers: TaskUpdated event when a task status changes to 'completed'
     */
    public function handle(TaskUpdated $event): void
    {
        $task = $event->task;

        // Only send notification if task was just completed
        if ($task->status === 'completed' && $task->wasChanged('status')) {
            $task->project->manager->notify(
                new TaskFlowNotification(
                    title: '✅ Task Completed',
                    message: "{$task->assignedUser->name} completed: {$task->title}",
                    actionUrl: route('tasks.show', $task),
                    actionLabel: 'View Task'
                )
            );
        }
    }
}
