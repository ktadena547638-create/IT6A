<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerformanceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_response_under_250ms()
    {
        $user = User::factory()->create();
        Project::factory(10)->create(['manager_id' => $user->id]);
        Task::factory(30)->create();

        $start = microtime(true);
        
        $response = $this->actingAs($user)->get('/dashboard');
        
        $duration = (microtime(true) - $start) * 1000; // Convert to ms

        $this->assertLessThan(250, $duration, 
            "Dashboard response time: {$duration}ms (target: <250ms)"
        );
        $this->assertEquals(200, $response->status());
    }

    public function test_project_list_response_under_250ms()
    {
        $user = User::factory()->admin()->create();
        Project::factory(30)->create();

        $start = microtime(true);
        
        $response = $this->actingAs($user)->get('/projects');
        
        $duration = (microtime(true) - $start) * 1000;

        $this->assertLessThan(250, $duration, 
            "Project list response time: {$duration}ms (target: <250ms)"
        );
    }

    public function test_task_list_response_under_250ms()
    {
        $user = User::factory()->admin()->create();
        Task::factory(50)->create();

        $start = microtime(true);
        
        $response = $this->actingAs($user)->get('/tasks');
        
        $duration = (microtime(true) - $start) * 1000;

        $this->assertLessThan(250, $duration, 
            "Task list response time: {$duration}ms (target: <250ms)"
        );
    }
}