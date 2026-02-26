<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1️⃣ Make sure client_id column exists
        if (!Schema::hasColumn('projects', 'client_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->unsignedBigInteger('client_id')
                      ->nullable()
                      ->after('name');
            });
        }

        // 2️⃣ Fix orphan client_id values BEFORE adding FK
        DB::statement("
            UPDATE projects
            SET client_id = NULL
            WHERE client_id IS NOT NULL
            AND client_id NOT IN (SELECT id FROM clients)
        ");

        // 3️⃣ Add foreign key safely only if it does not exist
        $fkExists = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'projects'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            AND CONSTRAINT_NAME = 'projects_client_id_foreign'
        ");

        if (empty($fkExists)) {
            DB::statement("
                ALTER TABLE projects
                ADD CONSTRAINT projects_client_id_foreign
                FOREIGN KEY (client_id)
                REFERENCES clients(id)
                ON DELETE SET NULL
            ");
        }

        // 4️⃣ Drop old client_name safely
        if (Schema::hasColumn('projects', 'client_name')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('client_name');
            });
        }
    }

    public function down(): void
    {
        // 1️⃣ Drop FK safely if exists
        $fkExists = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'projects'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            AND CONSTRAINT_NAME = 'projects_client_id_foreign'
        ");

        if (!empty($fkExists)) {
            DB::statement("
                ALTER TABLE projects
                DROP FOREIGN KEY projects_client_id_foreign
            ");
        }

        // 2️⃣ Restore columns safely
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'client_name')) {
                $table->string('client_name')->nullable();
            }

            if (Schema::hasColumn('projects', 'client_id')) {
                $table->dropColumn('client_id');
            }
        });
    }
};
