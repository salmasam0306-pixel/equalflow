<?php
// database/migrations/2026_09_12_000001_add_main_task_columns_to_tasks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'parent_task_id')) {
                $table->foreignId('parent_task_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('tasks')
                    ->onDelete('cascade');
            }

            if (!Schema::hasColumn('tasks', 'task_type')) {
                $table->enum('task_type', ['main', 'sub'])
                    ->default('main')
                    ->after('parent_task_id');
            }

            if (!Schema::hasColumn('tasks', 'assigned_by')) {
                $table->foreignId('assigned_by')
                    ->nullable()
                    ->after('created_by')
                    ->constrained('users')
                    ->onDelete('set null');
            }

            if (!Schema::hasColumn('tasks', 'priority')) {
                $table->enum('priority', ['low', 'medium', 'high'])
                    ->default('medium')
                    ->after('status');
            }
        });

        // Backfill existing tasks: they were all effectively "main" tasks.
        \DB::table('tasks')->whereNull('task_type')->update(['task_type' => 'main']);
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'parent_task_id')) {
                $table->dropForeign(['parent_task_id']);
                $table->dropColumn('parent_task_id');
            }
            if (Schema::hasColumn('tasks', 'task_type')) {
                $table->dropColumn('task_type');
            }
            if (Schema::hasColumn('tasks', 'assigned_by')) {
                $table->dropForeign(['assigned_by']);
                $table->dropColumn('assigned_by');
            }
            if (Schema::hasColumn('tasks', 'priority')) {
                $table->dropColumn('priority');
            }
        });
    }
};