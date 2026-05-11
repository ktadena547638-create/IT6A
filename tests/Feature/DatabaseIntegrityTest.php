<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_cascade_delete_project_deletes_tasks()
    {
        $project = Project::factory()->create();
        Task::factory(5)->create(['project_id' => $project->id]);

        $this->assertCount(5, Task::where('project_id', $project->id)->get());

        $project->delete();

        $this->assertCount(0, Task::where('project_id', $project->id)->get());
    }

    public function test_cascade_delete_task_deletes_comments()
    {
        $task = Task::factory()->create();
        TaskComment::factory(3)->create(['task_id' => $task->id]);

        $this->assertCount(3, TaskComment::where('task_id', $task->id)->get());

        $task->delete();

        $this->assertCount(0, TaskComment::where('task_id', $task->id)->get());
    }

    public function test_null_on_delete_assigned_user()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['assigned_user_id' => $user->id]);

        $this->assertEquals($user->id, $task->assigned_user_id);

        $user->delete();

        $task->refresh();
        $this->assertNull($task->assigned_user_id);
    }

    public function test_foreign_key_constraints_enforced()
    {
        try {
            Task::factory()->create(['project_id' => 99999]);
            $this->fail('Foreign key constraint should prevent invalid project_id');
        } catch (\Exception $e) {
            $this->assertStringContainsString('foreign key', strtolower($e->getMessage()));
        }
    }
}