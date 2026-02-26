<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up(): void
{
    // Step 1: Add columns only if they don't exist
    Schema::table('tasks', function (Blueprint $table) {
        if (!Schema::hasColumn('tasks', 'project_id')) {
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
        }

        if (!Schema::hasColumn('tasks', 'assigned_to')) {
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
        }

        if (!Schema::hasColumn('tasks', 'due_date')) {
            $table->date('due_date')->nullable();
        }
    });

    // Step 2: Fix invalid status values
    DB::table('tasks')
        ->whereNotIn('status', ['pending','in_progress','done'])
        ->update(['status' => 'pending']);

    // Step 3: Update enum safely
    DB::statement("ALTER TABLE tasks MODIFY status ENUM('pending','in_progress','done') NOT NULL DEFAULT 'pending'");
}

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');

            $table->dropForeign(['assigned_to']);
            $table->dropColumn('assigned_to');

            $table->dropColumn('due_date');

            // Note: reversing enum change might require raw SQL if necessary
        });
    }
};
