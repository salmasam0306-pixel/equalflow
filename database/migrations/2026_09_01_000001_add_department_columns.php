<?php
// database/migrations/2026_09_01_000001_add_department_columns.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add department_id to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')
                    ->nullable()
                    ->after('specialties')
                    ->constrained('departments')
                    ->onDelete('set null');
            }
            
            if (!Schema::hasColumn('users', 'is_profile_ready')) {
                $table->boolean('is_profile_ready')->default(false)->after('department_id');
            }
            
            if (!Schema::hasColumn('users', 'profile_completed_at')) {
                $table->timestamp('profile_completed_at')->nullable()->after('is_profile_ready');
            }
            
            // Remove old profile_completed column if exists
            if (Schema::hasColumn('users', 'profile_completed')) {
                $table->dropColumn('profile_completed');
            }
        });

        // Add department_id to tasks table
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'department_id')) {
                $table->foreignId('department_id')
                    ->nullable()
                    ->after('project_id')
                    ->constrained('departments')
                    ->onDelete('set null');
            }
        });

        // Update project_members table to remove role-based leader (since we're using departments)
        // This is optional - you can keep it for backward compatibility
        Schema::table('project_members', function (Blueprint $table) {
            // No changes needed, but ensure the table exists
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropColumn(['department_id', 'is_profile_ready', 'profile_completed_at']);
            }
            
            // Re-add profile_completed if needed
            if (!Schema::hasColumn('users', 'profile_completed')) {
                $table->boolean('profile_completed')->default(false);
            }
        });
    }
};