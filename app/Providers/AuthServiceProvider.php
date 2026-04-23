<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Models\TaskComment;
use App\Policies\TaskPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TaskCommentPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Task::class => TaskPolicy::class,
        Project::class => ProjectPolicy::class,
        TaskComment::class => TaskCommentPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register policy mappings
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // ✅ MASTER KEY: Global admin gate - centralized authority bypass
        // Returns true for admins (full access), null for non-admins (check specific policies)
        Gate::before(function (User $user) {
            if ($user->isAdmin()) {
                return true;
            }
            return null; // Allow specific policies to handle authorization
        });

        // Define additional global gates
        Gate::define('viewLogs', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manageUsers', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manageAllProjects', function (User $user) {
            return $user->isAdmin();
        });
    }
}
