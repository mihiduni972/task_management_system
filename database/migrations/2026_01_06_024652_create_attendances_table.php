<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // 🔹 Employer / Admin (attendance record belongs to)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            // 🔹 Attendance date
            $table->date('date');

            // 🔹 In / Out time
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();

            // 🔹 Total worked minutes
            $table->integer('worked_minutes')->nullable();

            // 🔹 Attendance status
            $table->enum('status', ['present', 'late', 'absent'])
                  ->default('present');

            // 🔹 Prevent duplicate attendance for same day
            $table->unique(['user_id', 'date']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
