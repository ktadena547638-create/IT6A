<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectMilestone
{
    use Dispatchable, SerializesModels;

    /**
     * ✅ PROJECT MILESTONE: Fire when project is assigned to a client
     */
    public function __construct(public Project $project)
    {
    }
}
