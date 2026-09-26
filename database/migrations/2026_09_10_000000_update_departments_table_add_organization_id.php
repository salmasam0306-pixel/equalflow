<?php
// database/migrations/2026_09_10_000000_update_departments_table_add_organization_id.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Check if project_id exists and drop it
            if (Schema::hasColumn('departments', 'project_id')) {
                $table->dropForeign(['project_id']);
                $table->dropColumn('project_id');
            }

            // Add organization_id if it doesn't exist
            if (!Schema::hasColumn('departments', 'organization_id')) {
                $table->foreignId('organization_id')
                    ->constrained('organizations')
                    ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'organization_id')) {
                $table->dropForeign(['organization_id']);
                $table->dropColumn('organization_id');
            }

            // Re-add project_id if needed (for rollback)
            if (!Schema::hasColumn('departments', 'project_id')) {
                $table->foreignId('project_id')
                    ->constrained('projects')
                    ->onDelete('cascade');
            }
        });
    }
};