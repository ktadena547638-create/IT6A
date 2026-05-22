<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;

class NewComment extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Task $task,
        public TaskComment $comment,
        public User $commentedBy
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
                'commented_by' => $this->commentedBy->name,
                'comment_preview' => substr($this->comment->comment, 0, 100) . (strlen($this->comment->comment) > 100 ? '...' : ''),
                'message' => "{$this->commentedBy->name} commented on task '{$this->task->title}'",
                'type' => 'new_comment',
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
            'commented_by' => $this->commentedBy->name,
            'comment_preview' => substr($this->comment->comment, 0, 100) . (strlen($this->comment->comment) > 100 ? '...' : ''),
            'message' => "{$this->commentedBy->name} commented on task '{$this->task->title}'",
            'type' => 'new_comment',
            'action_url' => route('tasks.show', $this->task),
        ];
    }
}

