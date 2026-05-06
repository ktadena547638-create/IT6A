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
     * ✅ REALISTIC LIVE DATA: Create a comprehensive development environment
     * - 1 Admin (The Sovereign)
     * - 3 Project Managers (The Generals)
     * - 10 Team Members (The Soldiers)
     * - 2 Clients (The Stakeholders)
     * - 3 Projects assigned to managers, linked to clients
     * - 30+ Tasks distributed across projects and team members
     */
    public function run(): void
    {
        // ====== CREATE USERS ======

        // 1 Admin
        $admin = User::factory()
            ->admin()
            ->create([
                'name' => 'Admin',
                'email' => 'admin',
                'password' => bcrypt('password'),
            ]);

        // 3 Project Managers
        $pm1 = User::factory()
            ->projectManager()
            ->create([
                'name' => 'Sarah',
                'email' => 'sarah',
                'password' => bcrypt('password'),
            ]);

        $pm2 = User::factory()
            ->projectManager()
            ->create([
                'name' => 'Michael',
                'email' => 'michael',
                'password' => bcrypt('password'),
            ]);

        $pm3 = User::factory()
            ->projectManager()
            ->create([
                'name' => 'Emma',
                'email' => 'emma',
                'password' => bcrypt('password'),
            ]);

        // 10 Team Members
        $teamMembers = collect([
            User::factory()->teamMember()->create(['name' => 'Alex', 'email' => 'alex', 'password' => bcrypt('password')]),
            User::factory()->teamMember()->create(['name' => 'Jessica', 'email' => 'jessica', 'password' => bcrypt('password')]),
            User::factory()->teamMember()->create(['name' => 'David', 'email' => 'david', 'password' => bcrypt('password')]),
            User::factory()->teamMember()->create(['name' => 'Lisa', 'email' => 'lisa', 'password' => bcrypt('password')]),
            User::factory()->teamMember()->create(['name' => 'James', 'email' => 'james', 'password' => bcrypt('password')]),
            User::factory()->teamMember()->create(['name' => 'Nicole', 'email' => 'nicole', 'password' => bcrypt('password')]),
            User::factory()->teamMember()->create(['name' => 'Robert', 'email' => 'robert', 'password' => bcrypt('password')]),
            User::factory()->teamMember()->create(['name' => 'Angela', 'email' => 'angela', 'password' => bcrypt('password')]),
            User::factory()->teamMember()->create(['name' => 'Marcus', 'email' => 'marcus', 'password' => bcrypt('password')]),
            User::factory()->teamMember()->create(['name' => 'Rachel', 'email' => 'rachel', 'password' => bcrypt('password')]),
        ]);

        // 2 Clients
        $client1 = User::factory()
            ->client()
            ->create([
                'name' => 'TechCorp',
                'email' => 'techcorp',
                'password' => bcrypt('password'),
            ]);

        $client2 = User::factory()
            ->client()
            ->create([
                'name' => 'Solutions',
                'email' => 'solutions',
                'password' => bcrypt('password'),
            ]);

        // ====== CREATE PROJECTS ======

        // Project 1: Website Redesign (PM1 + Client1)
        $project1 = Project::factory()
            ->create([
                'name' => 'Website Redesign Initiative',
                'description' => 'Complete redesign of company website with new UX/UI',
                'manager_id' => $pm1->id,
                'client_id' => $client1->id,
                'status' => 'active',
                'priority' => 'high',
            ]);

        // Project 2: Mobile App Development (PM2 + Client1)
        $project2 = Project::factory()
            ->create([
                'name' => 'Mobile App Development',
                'description' => 'Native iOS and Android application development',
                'manager_id' => $pm2->id,
                'client_id' => $client1->id,
                'status' => 'active',
                'priority' => 'critical',
            ]);

        // Project 3: Data Analytics Dashboard (PM3 + Client2)
        $project3 = Project::factory()
            ->create([
                'name' => 'Enterprise Data Analytics Dashboard',
                'description' => 'Real-time analytics dashboard for business intelligence',
                'manager_id' => $pm3->id,
                'client_id' => $client2->id,
                'status' => 'planning',
                'priority' => 'high',
            ]);

        // ====== CREATE TASKS ======

        // Project 1 Tasks (Website Redesign)
        $project1Tasks = [
            ['title' => 'Design wireframes and mockups', 'priority' => 'high', 'status' => 'completed'],
            ['title' => 'Setup development environment', 'priority' => 'high', 'status' => 'completed'],
            ['title' => 'Frontend development - Homepage', 'priority' => 'high', 'status' => 'in_progress'],
            ['title' => 'Frontend development - Product pages', 'priority' => 'high', 'status' => 'in_progress'],
            ['title' => 'Backend API integration', 'priority' => 'medium', 'status' => 'in_progress'],
            ['title' => 'Database schema design', 'priority' => 'medium', 'status' => 'completed'],
            ['title' => 'User authentication implementation', 'priority' => 'critical', 'status' => 'in_progress'],
            ['title' => 'Testing and QA', 'priority' => 'high', 'status' => 'pending'],
            ['title' => 'Performance optimization', 'priority' => 'medium', 'status' => 'pending'],
            ['title' => 'Launch and deployment', 'priority' => 'critical', 'status' => 'pending'],
        ];

        foreach ($project1Tasks as $idx => $taskData) {
            Task::factory()->create(array_merge($taskData, [
                'project_id' => $project1->id,
                'assigned_user_id' => $teamMembers[$idx % 10]->id,
                'created_by' => $pm1->id,
            ]));
        }

        // Project 2 Tasks (Mobile App)
        $project2Tasks = [
            ['title' => 'App architecture and design', 'priority' => 'critical', 'status' => 'completed'],
            ['title' => 'iOS development - Authentication', 'priority' => 'critical', 'status' => 'in_progress'],
            ['title' => 'Android development - Authentication', 'priority' => 'critical', 'status' => 'in_progress'],
            ['title' => 'API integration layer', 'priority' => 'high', 'status' => 'in_progress'],
            ['title' => 'Push notification system', 'priority' => 'medium', 'status' => 'pending'],
            ['title' => 'Offline data sync', 'priority' => 'high', 'status' => 'pending'],
            ['title' => 'iOS testing and optimization', 'priority' => 'high', 'status' => 'pending'],
            ['title' => 'Android testing and optimization', 'priority' => 'high', 'status' => 'pending'],
            ['title' => 'App store submission', 'priority' => 'medium', 'status' => 'pending'],
            ['title' => 'User documentation and onboarding', 'priority' => 'low', 'status' => 'pending'],
        ];

        foreach ($project2Tasks as $idx => $taskData) {
            Task::factory()->create(array_merge($taskData, [
                'project_id' => $project2->id,
                'assigned_user_id' => $teamMembers[($idx + 5) % 10]->id,
                'created_by' => $pm2->id,
            ]));
        }

        // Project 3 Tasks (Data Analytics)
        $project3Tasks = [
            ['title' => 'Requirements analysis and planning', 'priority' => 'high', 'status' => 'pending'],
            ['title' => 'Data warehouse design', 'priority' => 'high', 'status' => 'pending'],
            ['title' => 'ETL pipeline development', 'priority' => 'high', 'status' => 'pending'],
            ['title' => 'Dashboard UI mockups', 'priority' => 'medium', 'status' => 'pending'],
            ['title' => 'Real-time data processing', 'priority' => 'critical', 'status' => 'pending'],
            ['title' => 'Report generation module', 'priority' => 'medium', 'status' => 'pending'],
            ['title' => 'User access control system', 'priority' => 'high', 'status' => 'pending'],
            ['title' => 'Performance benchmarking', 'priority' => 'medium', 'status' => 'pending'],
            ['title' => 'Security audit and compliance', 'priority' => 'critical', 'status' => 'pending'],
            ['title' => 'Client training and deployment', 'priority' => 'medium', 'status' => 'pending'],
        ];

        foreach ($project3Tasks as $idx => $taskData) {
            Task::factory()->create(array_merge($taskData, [
                'project_id' => $project3->id,
                'assigned_user_id' => $teamMembers[($idx + 2) % 10]->id,
                'created_by' => $pm3->id,
            ]));
        }

        // ====== SUCCESS MESSAGE ======
        $this->command->info('');
        $this->command->info('✅ TASKFLOW LIVE ENVIRONMENT CREATED');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('👑 Admin User:');
        $this->command->info("   Email: {$admin->email}");
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('👤 Project Managers (3):');
        $this->command->info("   • {$pm1->name} ({$pm1->email})");
        $this->command->info("   • {$pm2->name} ({$pm2->email})");
        $this->command->info("   • {$pm3->name} ({$pm3->email})");
        $this->command->info('');
        $this->command->info('⚔️  Team Members (10):');
        foreach ($teamMembers->take(5) as $member) {
            $this->command->info("   • {$member->name}");
        }
        $this->command->info('   ... and 5 more');
        $this->command->info('');
        $this->command->info('🏢 Clients (2):');
        $this->command->info("   • {$client1->name}");
        $this->command->info("   • {$client2->name}");
        $this->command->info('');
        $this->command->info('📊 Projects (3):');
        $this->command->info("   • {$project1->name} → {$client1->name}");
        $this->command->info("   • {$project2->name} → {$client1->name}");
        $this->command->info("   • {$project3->name} → {$client2->name}");
        $this->command->info('');
        $this->command->info('✅ 30 Tasks created and distributed');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('Password for all users: password');
        $this->command->info('');
    }
}
