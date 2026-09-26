<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'outstation', 'annual_leave', 'medical_leave'])->default('active')->after('role');
            $table->date('status_until')->nullable()->after('status');
            $table->text('status_note')->nullable()->after('status_until');
            $table->timestamp('status_updated_at')->nullable()->after('status_note');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'status_until', 'status_note', 'status_updated_at']);
        });
    }
};