<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 🔹 Run the migrations.
     * Modify the 'assets' table:
     * - Change 'asset_type' from ENUM to string for more flexible types (laptop, mouse, keyboard, etc.)
     * - Add 'description' column if it doesn't exist
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Change 'asset_type' column type to string
            $table->string('asset_type')->change();
            
            // Add 'description' column if missing
            if (!Schema::hasColumn('assets', 'description')) {
                $table->text('description')->nullable()->after('asset_type');
            }
        });
    }

    /**
     * 🔹 Reverse the migrations.
     * Revert the 'assets' table back to original:
     * - 'asset_type' as ENUM with limited options
     * - Drop 'description' column
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->enum('asset_type', ['hardware', 'software'])->change();
            $table->dropColumn('description');
        });
    }
};