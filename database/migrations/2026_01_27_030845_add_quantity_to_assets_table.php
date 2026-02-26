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
        Schema::table('assets', function (Blueprint $table) {
            // Adding quantity after asset_type
            $table->integer('quantity')->default(1)->after('asset_type');
            
            // Adding assigned_to column after status to track user assignment
            $table->unsignedBigInteger('assigned_to')->nullable()->after('status');
            
            // Optional: Uncomment the line below to enforce a database relationship
            // $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Dropping the columns if the migration is rolled back
            $table->dropColumn(['quantity', 'assigned_to']);
        });
    }
};