<?php

namespace App\Observers;

use App\Models\TaskComment;
use App\Notifications\NewComment;

class TaskCommentObserver
{
    /**
     * Handle the TaskComment "created" event.
     */
    public function created(TaskComment $comment): void
    {
        if (!auth()->check()) {
            return;
        }

        // Get all users related to the task who should be notified
        $task = $comment->task;
        
        // Notify task assignee (if not the commenter)
        if ($task->assignedUser && $task->assignedUser->id !== auth()->id()) {
            $task->assignedUser->notify(new NewComment($task, $comment, auth()->user()));
        }

        // Notify task creator (if not the commenter)
        if ($task->creator && $task->creator->id !== auth()->id()) {
            $task->creator->notify(new NewComment($task, $comment, auth()->user()));
        }

        // Notify project manager (if not the commenter)
        if ($task->project->manager && $task->project->manager->id !== auth()->id()) {
            $task->project->manager->notify(new NewComment($task, $comment, auth()->user()));
        }
    }
}

