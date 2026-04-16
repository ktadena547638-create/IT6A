<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\Project;
use App\Models\TaskComment;
use App\Observers\TaskObserver;
use App\Observers\ProjectObserver;
use App\Observers\TaskCommentObserver;
use App\Policies\TaskPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TaskCommentPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Observers
        Task::observe(TaskObserver::class);
        Project::observe(ProjectObserver::class);
        TaskComment::observe(TaskCommentObserver::class);

        // Register Policies
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(TaskComment::class, TaskCommentPolicy::class);
    }
}
