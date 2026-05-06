<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrationAndTriggerTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_migrations_run_successfully()
    {
        // Verify all tables exist
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('projects'));
        $this->assertTrue(Schema::hasTable('tasks'));
        $this->assertTrue(Schema::hasTable('task_activities'));
        $this->assertTrue(Schema::hasTable('task_comments'));
        $this->assertTrue(Schema::hasTable('task_attachments'));
        $this->assertTrue(Schema::hasTable('audit_logs'));
    }

    public function test_all_four_triggers_deployed()
    {
        $triggers = DB::select('SHOW TRIGGERS');
        $triggerNames = collect($triggers)->pluck('Trigger')->toArray();
        
        $this->assertContains('prevent_active_project_deletion', $triggerNames);
        $this->assertContains('auto_complete_project_on_tasks_done', $triggerNames);
        $this->assertContains('prevent_critical_overload', $triggerNames);
        $this->assertContains('prevent_critical_overload_on_update', $triggerNames);
        
        $this->assertCount(4, $triggers);
    }

    public function test_trigger_prevent_active_project_deletion()
    {
        $user = \App\Models\User::factory()->create();
        $project = \App\Models\Project::factory()->create([
            'manager_id' => $user->id,
            'status' => 'active'
        ]);

        try {
            $project->delete();
            $this->fail('Trigger should have prevented deletion of active project');
        } catch (\Exception $e) {
            $this->assertStringContainsString('DATABASE INTEGRITY VIOLATION', $e->getMessage());
        }
    }

    public function test_trigger_prevent_critical_overload()
    {
        $user = \App\Models\User::factory()->create();
        $project = \App\Models\Project::factory()->create();

        // Create 5 critical tasks
        for ($i = 0; $i < 5; $i++) {
            \App\Models\Task::factory()->create([
                'project_id' => $project->id,
                'assigned_user_id' => $user->id,
                'priority' => 'critical',
                'status' => 'pending'
            ]);
        }

        // 6th critical task should fail
        try {
            \App\Models\Task::factory()->create([
                'project_id' => $project->id,
                'assigned_user_id' => $user->id,
                'priority' => 'critical',
                'status' => 'pending'
            ]);
            $this->fail('Trigger should have prevented 6th critical task');
        } catch (\Exception $e) {
            $this->assertStringContainsString('CAPACITY LIMIT EXCEEDED', $e->getMessage());
        }
    }
}