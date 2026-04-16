<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations - Remove all seeded test data
     * This deletes:
     * - Admin (admin@test.com)
     * - Project Managers (pm1@test.com, pm2@test.com)
     * - Team Members (team1@test.com through team5@test.com)
     * - All associated Projects, Tasks, Activities, Comments
     */
    public function up(): void
    {
        // Disable FK constraints temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Identify seeded user IDs by email patterns
            $seededEmails = [
                'admin@test.com',
                'pm1@test.com',
                'pm2@test.com',
                'team1@test.com',
                'team2@test.com',
                'team3@test.com',
                'team4@test.com',
                'team5@test.com',
            ];

            // Get IDs of seeded users
            $seededUserIds = DB::table('users')
                ->whereIn('email', $seededEmails)
                ->pluck('id')
                ->toArray();

            if (!empty($seededUserIds)) {
                // Delete task attachments (cascading cleanup)
                DB::table('task_attachments')
                    ->whereIn('task_id', function ($query) use ($seededUserIds) {
                        $query->select('id')
                            ->from('tasks')
                            ->whereIn('project_id', function ($q) use ($seededUserIds) {
                                $q->select('id')
                                    ->from('projects')
                                    ->whereIn('manager_id', $seededUserIds);
                            });
                    })
                    ->delete();

                // Delete task comments
                DB::table('task_comments')
                    ->whereIn('task_id', function ($query) use ($seededUserIds) {
                        $query->select('id')
                            ->from('tasks')
                            ->whereIn('project_id', function ($q) use ($seededUserIds) {
                                $q->select('id')
                                    ->from('projects')
                                    ->whereIn('manager_id', $seededUserIds);
                            });
                    })
                    ->delete();

                // Delete task activities
                DB::table('task_activities')
                    ->whereIn('task_id', function ($query) use ($seededUserIds) {
                        $query->select('id')
                            ->from('tasks')
                            ->whereIn('project_id', function ($q) use ($seededUserIds) {
                                $q->select('id')
                                    ->from('projects')
                                    ->whereIn('manager_id', $seededUserIds);
                            });
                    })
                    ->delete();

                // Delete tasks belonging to seeded projects
                DB::table('tasks')
                    ->whereIn('project_id', function ($query) use ($seededUserIds) {
                        $query->select('id')
                            ->from('projects')
                            ->whereIn('manager_id', $seededUserIds);
                    })
                    ->delete();

                // Delete projects managed by seeded users
                DB::table('projects')
                    ->whereIn('manager_id', $seededUserIds)
                    ->delete();

                // Delete seeded users
                DB::table('users')
                    ->whereIn('email', $seededEmails)
                    ->delete();

                // Reset auto-increment counters
                DB::statement('ALTER TABLE users AUTO_INCREMENT = 1;');
                DB::statement('ALTER TABLE projects AUTO_INCREMENT = 1;');
                DB::statement('ALTER TABLE tasks AUTO_INCREMENT = 1;');
                DB::statement('ALTER TABLE task_comments AUTO_INCREMENT = 1;');
                DB::statement('ALTER TABLE task_activities AUTO_INCREMENT = 1;');
                DB::statement('ALTER TABLE task_attachments AUTO_INCREMENT = 1;');
            }
        } finally {
            // Re-enable FK constraints
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * Reverse the migrations - Cannot restore seeded data
     */
    public function down(): void
    {
        // Irreversible migration - seeded data cannot be restored
        // Run php artisan db:seed to regenerate test data if needed
    }
};
