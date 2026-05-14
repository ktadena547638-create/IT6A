<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HybridIntegrityTriggersTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Project $activeProject;
    protected Project $planningProject;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user using factory (ensures role is valid)
        $this->user = User::factory()->teamMember()->create([
            'name' => 'Test User',
            'email' => 'trigger-test@test.com',
        ]);

        // Create active project
        $this->activeProject = Project::factory()->create([
            'name' => 'Active Test Project',
            'description' => 'For trigger testing',
            'manager_id' => $this->user->id,
            'status' => 'active',
            'priority' => 'medium',
        ]);

        // Create planning project
        $this->planningProject = Project::factory()->create([
            'name' => 'Planning Test Project',
            'description' => 'For trigger testing',
            'manager_id' => $this->user->id,
            'status' => 'planning',
            'priority' => 'medium',
        ]);
    }

    /**
     * TEST 1: Trigger prevent_active_project_deletion
     * EXPECTED: Cannot delete active project
     */
    public function test_trigger_prevents_active_project_deletion()
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionMessageMatches('/DATABASE INTEGRITY VIOLATION/');

        $this->activeProject->delete();
    }

    /**
     * TEST 2: Trigger allows planning project deletion
     * EXPECTED: Can delete planning project (not active, not critical)
     */
    public function test_trigger_allows_planning_project_deletion()
    {
        // Planning projects with non-critical priority should be deletable
        $this->planningProject->delete();
        
        $this->assertNull(Project::find($this->planningProject->id));
    }

    /**
     * TEST 3: Trigger auto_complete_project_on_tasks_done
     * EXPECTED: Project auto-completes when all tasks are completed
     */
    public function test_trigger_auto_completes_project_when_all_tasks_done()
    {
        // Create a project with 2 tasks (status must be 'active', not 'in_progress')
        $project = Project::factory()->create([
            'name' => 'Test Auto-Complete Project',
            'description' => 'For auto-complete testing',
            'manager_id' => $this->user->id,
            'status' => 'active',
            'priority' => 'low',
        ]);

        $task1 = Task::factory()->create([
            'project_id' => $project->id,
            'title' => 'Task 1',
            'description' => 'First task',
            'created_by' => $this->user->id,
            'assigned_user_id' => $this->user->id,
            'status' => 'pending',
            'priority' => 'low',
        ]);

        $task2 = Task::factory()->create([
            'project_id' => $project->id,
            'title' => 'Task 2',
            'description' => 'Second task',
            'created_by' => $this->user->id,
            'assigned_user_id' => $this->user->id,
            'status' => 'pending',
            'priority' => 'low',
        ]);

        // Complete first task
        $task1->update(['status' => 'completed']);
        $project->refresh();
        $this->assertEquals('active', $project->status, 'Project should still be active');

        // Complete second task - should trigger auto-complete
        $task2->update(['status' => 'completed']);
        $project->refresh();
        $this->assertEquals('completed', $project->status, 'Project should be auto-completed');
    }

    /**
     * TEST 4: Trigger prevents critical overload on INSERT
     * EXPECTED: Cannot insert 6th critical task for user
     */
    public function test_trigger_prevents_critical_overload_on_insert()
    {
        // Create 5 critical tasks for the user
        for ($i = 1; $i <= 5; $i++) {
            Task::factory()->create([
                'project_id' => $this->activeProject->id,
                'title' => "Critical Task $i",
                'description' => 'Critical priority',
                'created_by' => $this->user->id,
                'assigned_user_id' => $this->user->id,
                'status' => 'pending',
                'priority' => 'critical',
            ]);
        }

        // Try to insert 6th critical task - should fail
        $this->expectException(QueryException::class);
        $this->expectExceptionMessageMatches('/CAPACITY LIMIT EXCEEDED/');

        Task::factory()->create([
            'project_id' => $this->activeProject->id,
            'title' => 'Critical Task 6',
            'description' => 'This should fail',
            'created_by' => $this->user->id,
            'assigned_user_id' => $this->user->id,
            'status' => 'pending',
            'priority' => 'critical',
        ]);
    }

    /**
     * TEST 5: Trigger prevents critical overload on UPDATE (priority change)
     * EXPECTED: Cannot change 6th task to critical when user already has 5 critical
     */
    public function test_trigger_prevents_critical_overload_on_priority_update()
    {
        // Create 5 critical tasks
        for ($i = 1; $i <= 5; $i++) {
            Task::factory()->create([
                'project_id' => $this->activeProject->id,
                'title' => "Critical Task $i",
                'description' => 'Critical priority',
                'created_by' => $this->user->id,
                'assigned_user_id' => $this->user->id,
                'status' => 'pending',
                'priority' => 'critical',
            ]);
        }

        // Create non-critical task
        $normalTask = Task::factory()->create([
            'project_id' => $this->activeProject->id,
            'title' => 'Normal Task',
            'description' => 'Medium priority',
            'created_by' => $this->user->id,
            'assigned_user_id' => $this->user->id,
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        // Try to change to critical - should fail
        $this->expectException(QueryException::class);
        $this->expectExceptionMessageMatches('/CAPACITY LIMIT EXCEEDED/');

        $normalTask->update(['priority' => 'critical']);
    }

    /**
     * TEST 6: Verify task_activities not written by triggers
     * EXPECTED: Only Observer writes audit entries, no trigger duplicates
     */
    /**
     * TEST 6: Verify task_activities table structure
     * EXPECTED: Table exists and is properly structured
     */
    public function test_task_activities_table_exists_with_proper_structure()
    {
        // Verify table exists
        $this->assertTrue(Schema::hasTable('task_activities'), 'task_activities table should exist');
        
        // Verify required columns exist
        $this->assertTrue(Schema::hasColumn('task_activities', 'id'));
        $this->assertTrue(Schema::hasColumn('task_activities', 'task_id'));
        $this->assertTrue(Schema::hasColumn('task_activities', 'user_id'));
        $this->assertTrue(Schema::hasColumn('task_activities', 'activity_type'));
        $this->assertTrue(Schema::hasColumn('task_activities', 'description'));
        $this->assertTrue(Schema::hasColumn('task_activities', 'created_at'));
    }

    /**
     * TEST 7: Verify basic task operations don't break constraints
     * EXPECTED: Creating and updating tasks completes without SQL errors
     */
    public function test_task_operations_complete_successfully()
    {
        // Create task with all required fields
        $task = Task::factory()->create([
            'project_id' => $this->activeProject->id,
            'created_by' => $this->user->id,
            'assigned_user_id' => $this->user->id,
        ]);
        
        $this->assertNotNull($task->id, 'Task should be created successfully');
        
        // Update task status - this triggers the SQL trigger
        $task->update(['status' => 'in_progress']);
        $task->refresh();
        
        $this->assertEquals('in_progress', $task->status, 'Task status should update successfully');
    }

    /**
     * TEST 8: Verify triggers exist in database
     * EXPECTED: All 4 triggers present and active
     */
    public function test_all_triggers_created_in_database()
    {
        $triggers = DB::select("SHOW TRIGGERS FROM task_management_system");
        $triggerNames = array_map(fn($t) => $t->Trigger, $triggers);

        $this->assertContains('prevent_active_project_deletion', $triggerNames);
        $this->assertContains('auto_complete_project_on_tasks_done', $triggerNames);
        $this->assertContains('prevent_critical_overload', $triggerNames);
        $this->assertContains('prevent_critical_overload_on_update', $triggerNames);
    }

    /**
     * Cleanup
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
