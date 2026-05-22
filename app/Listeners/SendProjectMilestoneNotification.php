<?php

namespace App\Listeners;

use App\Models\Project;
use App\Notifications\TaskFlowNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendProjectMilestoneNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * ✅ MILESTONE ALERT: Notify client when project is assigned to them
     * Triggers: When project.client_id is updated
     */
    public function __invoke(Project $project): void
    {
        // Only send notification if project has a client assigned
        if ($project->client_id) {
            $project->client->notify(
                new TaskFlowNotification(
                    title: '🏢 Project Assigned',
                    message: "Project '{$project->name}' has been assigned to you",
                    actionUrl: route('client.dashboard'),
                    actionLabel: 'View Projects'
                )
            );
        }
    }
}

