<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->json('skills_required')->nullable()->after('description');
            $table->string('experience_level')->nullable()->after('skills_required');
            $table->integer('estimated_hours')->nullable()->after('experience_level');
            $table->boolean('is_assigned')->default(false)->after('status');
            $table->timestamp('assigned_at')->nullable()->after('is_assigned');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['skills_required', 'experience_level', 'estimated_hours', 'is_assigned', 'assigned_at']);
        });
    }
};