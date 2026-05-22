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

    public function test_project_without_description_safe()
    {
        $project = Project::factory()->create(['description' => null]);
        
        $this->assertNull($project->description);
        $desc = $project->description ?? 'No description';
        $this->assertEquals('No description', $desc);
    }

    public function test_task_without_description_safe()
    {
        $task = Task::factory()->create(['description' => null]);
        
        $this->assertNull($task->description);
        $desc = $task->description ?? 'No details';
        $this->assertEquals('No details', $desc);
    }

    public function test_project_description_nullable_field()
    {
        $project = Project::factory()->create();
        
        // description is nullable - test safe access
        $text = $project->description ? substr($project->description, 0, 10) : 'N/A';
        $this->assertIsString($text);
    }
}
