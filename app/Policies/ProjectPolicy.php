<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any projects.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the project.
     * ✅ ADMIN BYPASS + Role-based logic
     */
    public function view(User $user, Project $project): bool
    {
        // Admins can view all projects
        if ($user->isAdmin()) {
            return true;
        }
        
        // Non-admins must have email verified
        if (!$user->email_verified_at) {
            return false;
        }
        
        // Project manager can view their own projects
        if ($user->id === $project->manager_id) {
            return true;
        }
        
        // Team members can view projects they're assigned tasks in
        return $project->tasks()
            ->where('assigned_user_id', $user->id)
            ->exists();
    }

    /**
     * Determine whether the user can create projects.
     * ✅ ADMIN BYPASS + Project managers
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isProjectManager();
    }

    /**
     * Determine whether the user can update the project.
     * ✅ ADMIN BYPASS + Project manager logic
     */
    public function update(User $user, Project $project): bool
    {
        return $user->isAdmin() || $user->id === $project->manager_id;
    }

    /**
     * Determine whether the user can delete the project.
     * ✅ ADMIN BYPASS + Project manager logic
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->isAdmin() || $user->id === $project->manager_id;
    }

    /**
     * Determine whether the user can manage project tasks.
     * ✅ ADMIN BYPASS + Project manager logic
     */
    public function manageTasks(User $user, Project $project): bool
    {
        return $user->isAdmin() || $user->id === $project->manager_id;
    }
}