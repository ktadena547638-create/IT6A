<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NullSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_without_assigned_user_safe()
    {
        $task = Task::factory()->create(['assigned_user_id' => null]);
        
        // Should not throw error
        $this->assertNull($task->assignedUser);
        $name = $task->assignedUser?->name ?? 'Unassigned';
        $this->assertEquals('Unassigned', $name);
    }

    public function test_project_without_due_date_safe()
    {
        $project = Project::factory()->create(['due_date' => null]);
        
        $this->assertNull($project->due_date);
        $date = $project->due_date?->format('M d, Y') ?? 'No date';
        $this->assertEquals('No date', $date);
    }

    public function test_project_without_manager_safe()
    {
        $project = Project::factory()->create(['manager_id' => null]);
        
        $this->assertNull($project->manager);
        $name = $project->manager?->name ?? 'Unassigned';
        $this->assertEquals('Unassigned', $name);
    }

    public function test_task_without_created_by_safe()
    {
        $task = Task::factory()->create(['created_by' => null]);
        
        $creator = $task->creator?->name ?? 'System';
        $this->assertEquals('System', $creator);
    }
}