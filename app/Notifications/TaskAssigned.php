<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;
use App\Models\User;

class TaskAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Task $task,
        public User $assignedBy
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage(
            [
                'task_id' => $this->task->id,
                'task_title' => $this->task->title,
                'project_name' => $this->task->project->name,
                'assigned_by' => $this->assignedBy->name,
                'due_date' => $this->task->due_date->format('M d, Y'),
                'priority' => $this->task->priority,
                'message' => "{$this->assignedBy->name} assigned you the task '{$this->task->title}' in project '{$this->task->project->name}'",
                'type' => 'task_assigned',
                'action_url' => route('tasks.show', $this->task),
            ]
        );
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'project_name' => $this->task->project->name,
            'assigned_by' => $this->assignedBy->name,
            'due_date' => $this->task->due_date->format('M d, Y'),
            'priority' => $this->task->priority,
            'message' => "{$this->assignedBy->name} assigned you the task '{$this->task->title}' in project '{$this->task->project->name}'",
            'type' => 'task_assigned',
            'action_url' => route('tasks.show', $this->task),
        ];
    }
}
