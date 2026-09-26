<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'depends_on')) {
                $table->dropForeign(['depends_on']);
                $table->dropColumn('depends_on');
            }
            if (Schema::hasColumn('tasks', 'dependency_type')) {
                $table->dropColumn('dependency_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('depends_on')->nullable()->constrained('tasks')->onDelete('set null');
            $table->enum('dependency_type', ['finish_to_start', 'start_to_start', 'finish_to_finish'])->default('finish_to_start');
        });
    }
};