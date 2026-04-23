<?php

namespace App\Providers;

use App\Events\ProjectMilestone;
use App\Events\TaskCreated;
use App\Events\TaskUpdated;
use App\Listeners\SendProjectMilestoneNotification;
use App\Listeners\SendTaskAssignedNotification;
use App\Listeners\SendTaskCompletedNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        TaskCreated::class => [
            SendTaskAssignedNotification::class,
        ],
        TaskUpdated::class => [
            SendTaskCompletedNotification::class,
        ],
        ProjectMilestone::class => [
            SendProjectMilestoneNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
