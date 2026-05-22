<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_projects()
    {
        $admin = User::factory()->admin()->create();
        Project::factory(5)->create();

        $response = $this->actingAs($admin)->get('/projects');
        
        $this->assertEquals(200, $response->status());
    }

    public function test_project_manager_cannot_view_others_projects()
    {
        $pm1 = User::factory()->projectManager()->create();
        $pm2 = User::factory()->projectManager()->create();
        
        $project = Project::factory()->create(['manager_id' => $pm1->id]);

        $response = $this->actingAs($pm2)->get("/projects/{$project->id}");
        
        $this->assertEquals(403, $response->status());
    }

    public function test_team_member_cannot_create_project()
    {
        $member = User::factory()->teamMember()->create();

        $response = $this->actingAs($member)->get('/projects/create');
        
        $this->assertEquals(403, $response->status());
    }

    public function test_project_manager_can_create_task()
    {
        $pm = User::factory()->projectManager()->create();
        $project = Project::factory()->create(['manager_id' => $pm->id]);

        $response = $this->actingAs($pm)->post('/tasks', [
            'title' => 'Test Task',
            'project_id' => $project->id,
            'priority' => 'high',
            'status' => 'pending'
        ]);
        
        $this->assertNotEquals(403, $response->status());
    }
}
