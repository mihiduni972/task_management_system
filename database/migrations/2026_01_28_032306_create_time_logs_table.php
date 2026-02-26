<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 🔹 Run the migrations.
     * Creates the 'time_logs' table to track work hours per task.
     */
    public function up(): void
    {
        Schema::create('time_logs', function (Blueprint $table) {
            $table->id();

            // 🔹 Foreign key to tasks table (which task this log belongs to), cascade on delete
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade'); 

            // 🔹 Foreign key to users table (who logged the time), cascade on delete
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 

            // 🔹 Hours spent, allows decimal values like 2.5
            $table->decimal('hours', 5, 2); 

            // 🔹 Work description (optional)
            $table->text('description')->nullable();

            // 🔹 The date when the work was done
            $table->date('log_date');

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * 🔹 Reverse the migrations.
     * Drops the 'time_logs' table if it exists
     */
    public function down(): void
    {
        Schema::dropIfExists('time_logs');
    }
};