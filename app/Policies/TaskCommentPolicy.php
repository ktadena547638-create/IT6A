<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TaskComment;

class TaskCommentPolicy
{
    /**
     * Determine whether the user can view any comments.
     */
    public function viewAny(User $user): bool
    {
        return false; // Comments are viewed per-task, not globally
    }

    /**
     * Determine whether the user can view the comment.
     */
    public function view(User $user, TaskComment $comment): bool
    {
        // User can view comment if they have access to the task
        $task = $comment->task;
        return $user->isAdmin() || 
               $user->id === $task->assigned_user_id || 
               $user->id === $task->created_by ||
               $user->id === $task->project->manager_id;
    }

    /**
     * Determine whether the user can create comments.
     */
    public function create(User $user): bool
    {
        // Must be verified user
        return $user->email_verified_at !== null;
    }

    /**
     * Determine whether the user can update the comment.
     */
    public function update(User $user, TaskComment $comment): bool
    {
        return $user->isAdmin() || $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can delete the comment.
     */
    public function delete(User $user, TaskComment $comment): bool
    {
        return $user->isAdmin() || $user->id === $comment->user_id;
    }
}
