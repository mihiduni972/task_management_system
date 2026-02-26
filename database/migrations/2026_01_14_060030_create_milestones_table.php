<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 🔹 Run the migrations.
     * Creates the 'milestones' table with project relationship and progress tracking
     */
    public function up(): void
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();

            // 🔹 Foreign key to projects table, cascade on delete
            $table->foreignId('project_id')
                  ->constrained('projects')
                  ->onDelete('cascade');

            $table->string('title'); // Milestone title
            $table->integer('progress')->default(0); // Progress percentage (0-100)
            $table->date('due_date')->nullable(); // Optional due date

            // 🔹 Milestone status: 'ongoing' or 'completed'
            $table->enum('status', ['ongoing', 'completed'])->default('ongoing');

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * 🔹 Reverse the migrations.
     * Drops the 'milestones' table if it exists
     */
    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};