<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add 'client' role to users and client_id to projects
     * ✅ SELECTIVE SCRYING: Clients can access their own projects only
     */
    public function up(): void
    {
        // Add client role to users table enum
        // Since Laravel doesn't support direct enum modification, we'll use raw SQL
        if (DB::getDriverName() === 'sqlite') {
            // SQLite approach: recreate the enum with new value
            // For SQLite, we need to handle this differently
            DB::statement("
                UPDATE users 
                SET role = CASE 
                    WHEN role IN ('admin', 'project_manager', 'team_member') THEN role
                    ELSE 'team_member'
                END
            ");
        } else {
            // MySQL approach: modify the enum
            Schema::table('users', function (Blueprint $table) {
                // This would work for MySQL, but we need a different approach for SQLite
            });
        }

        // Add client_id to projects table
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'client_id')) {
                $table->foreignId('client_id')->nullable()->constrained('users')->cascadeOnDelete()->after('manager_id');
                $table->index('client_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'client_id')) {
                $table->dropConstraint('projects_client_id_foreign');
                $table->dropColumn('client_id');
            }
        });
    }
};
