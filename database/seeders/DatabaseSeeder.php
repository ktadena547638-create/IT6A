<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 1 Admin
        $admin = User::factory()
            ->admin()
            ->create([
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
            ]);

        // Create 2 Project Managers
        $pm1 = User::factory()
            ->projectManager()
            ->create([
                'name' => 'Project Manager One',
                'email' => 'pm1@test.com',
                'password' => bcrypt('password'),
            ]);

        $pm2 = User::factory()
            ->projectManager()
            ->create([
                'name' => 'Project Manager Two',
                'email' => 'pm2@test.com',
                'password' => bcrypt('password'),
            ]);

        // Create 5 Team Members
        $teamMembers = User::factory()
            ->teamMember()
            ->count(5)
            ->create([
                'password' => bcrypt('password'),
            ]);

        // Update team member emails
        $teamMembers[0]->update(['email' => 'team1@test.com', 'name' => 'Team Member One']);
        $teamMembers[1]->update(['email' => 'team2@test.com', 'name' => 'Team Member Two']);
        $teamMembers[2]->update(['email' => 'team3@test.com', 'name' => 'Team Member Three']);
        $teamMembers[3]->update(['email' => 'team4@test.com', 'name' => 'Team Member Four']);
        $teamMembers[4]->update(['email' => 'team5@test.com', 'name' => 'Team Member Five']);

        // Create 5 Projects distributed between the 2 Project Managers
        $projects = [];

        // PM1 gets 3 projects
        for ($i = 1; $i <= 3; $i++) {
            $projects[] = Project::factory()
                ->create(['manager_id' => $pm1->id]);
        }

        // PM2 gets 2 projects
        for ($i = 1; $i <= 2; $i++) {
            $projects[] = Project::factory()
                ->create(['manager_id' => $pm2->id]);
        }

        // Create 30 Tasks randomly distributed across projects and team members
        foreach ($projects as $project) {
            for ($i = 1; $i <= 6; $i++) {
                Task::factory()
                    ->create([
                        'project_id' => $project->id,
                        'assigned_user_id' => $teamMembers[rand(0, 4)]->id,
                        'created_by' => fake()->randomElement([$pm1->id, $pm2->id]),
                    ]);
            }
        }

        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->info('   - 1 Admin, 2 Project Managers, 5 Team Members');
        $this->command->info('   - 5 Projects with varied statuses');
        $this->command->info('   - 30 Tasks with mixed priorities and statuses');
        $this->command->info('   - 30+ TaskActivity records (auto-logged)');
    }
}
