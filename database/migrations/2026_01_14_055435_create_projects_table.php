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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // Project Name

            // 🔗 Client relation (NEW - Correct way)
            $table->foreignId('client_id')
                  ->constrained('clients')
                  ->onDelete('cascade');

            $table->text('description')->nullable(); // Project Description
            $table->date('start_date')->nullable(); // Start Date
            $table->date('end_date')->nullable(); // End Date

            $table->enum('status', ['ongoing', 'completed', 'on-hold'])
                  ->default('ongoing'); // Project Status

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->onDelete('cascade'); // Admin who created

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
