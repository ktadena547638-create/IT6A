<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class QueryOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_controller_no_n_plus_one()
    {
        $user = User::factory()->create();
        Project::factory(5)->create(['manager_id' => $user->id]);

        DB::enableQueryLog();
        
        $projects = Project::where('manager_id', $user->id)
            ->with(['manager:id,name', 'tasks:id,project_id,status'])
            ->get();
        
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        
        // Should be ~2 queries (1 projects, 1 for relationship), not 6+
        $this->assertLessThan(5, $queryCount, 
            "Found $queryCount queries. Expected <5 for N+1 prevention"
        );
    }

    public function test_task_list_controller_no_n_plus_one()
    {
        $user = User::factory()->admin()->create();
        Task::factory(10)->create();

        DB::enableQueryLog();
        
        $tasks = Task::with(['project', 'assignedUser', 'creator'])->get();
        
        $queryCount = count(DB::getQueryLog());
        
        $this->assertLessThan(5, $queryCount, 
            "Task list has N+1 pattern with $queryCount queries"
        );
    }

    public function test_project_list_no_n_plus_one()
    {
        Project::factory(10)->create();

        DB::enableQueryLog();
        
        $projects = Project::with(['manager:id,name'])->withCount('tasks')->get();
        
        $queryCount = count(DB::getQueryLog());
        
        $this->assertLessThan(5, $queryCount, 
            "Project list has N+1 pattern with $queryCount queries"
        );
    }
}