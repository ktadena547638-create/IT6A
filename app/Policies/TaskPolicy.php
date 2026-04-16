<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;

class TaskPolicy
{
    /**
     * Determine whether the user can view any tasks.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the task.
     * ✅ ADMIN BYPASS + Role-based logic
     */
    public function view(User $user, Task $task): bool
    {
        // Admins can view all tasks
        if ($user->isAdmin()) {
            return true;
        }
        
        // Task creator can always view their own tasks
        if ($user->id === $task->created_by) {
            return true;
        }
        
        // Assigned user can view the task
        if ($user->id === $task->assigned_user_id) {
            return true;
        }
        
        // Project manager can view tasks in their projects
        if ($task->project && $user->id === $task->project->manager_id) {
            return true;
        }
        
        // Fallback: deny access
        return false;
    }

    /**
     * Determine whether the user can create tasks.
     * ✅ PROJECT MANAGERS (or admins with global bypass)
     */
    public function create(User $user): bool
    {
        return $user->isProjectManager() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the task.
     * ✅ ADMIN BYPASS + Creator/Manager logic
     */
    public function update(User $user, Task $task): bool
    {
        // Admins can update all tasks
        if ($user->isAdmin()) {
            return true;
        }
        
        // Task creator can update
        if ($user->id === $task->created_by) {
            return true;
        }
        
        // Project manager can update tasks in their projects
        if (!$task->relationLoaded('project')) {
            $task->load('project');
        }
        
        return $task->project && $user->id === $task->project->manager_id;
    }

    /**
     * Determine whether the user can delete the task.
     * ✅ ADMIN BYPASS + Creator/Manager logic
     */
    public function delete(User $user, Task $task): bool
    {
        // Admins can delete all tasks
        if ($user->isAdmin()) {
            return true;
        }
        
        // Task creator can delete
        if ($user->id === $task->created_by) {
            return true;
        }
        
        // Project manager can delete tasks in their projects
        if (!$task->relationLoaded('project')) {
            $task->load('project');
        }
        
        return $task->project && $user->id === $task->project->manager_id;
    }

    /**
     * Determine whether the user can reassign the task.
     * ✅ STREAMLINED: Project manager can reassign (admin bypass global)
     */
    public function reassign(User $user, Task $task): bool
    {
        // Ensure project is loaded to avoid extra query
        if (!$task->relationLoaded('project')) {
            $task->load('project');
        }
        
        return $task->project && $user->id === $task->project->manager_id;
    }

    /**
     * Determine whether the user can complete the task.
     * ✅ ADMIN BYPASS + Assigned user logic
     */
    public function complete(User $user, Task $task): bool
    {
        // Admins can complete all tasks
        if ($user->isAdmin()) {
            return true;
        }
        
        return $user->id === $task->assigned_user_id;
    }
}