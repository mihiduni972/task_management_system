<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        DB::statement("
            ALTER TABLE users 
            MODIFY user_type ENUM('admin','employer','attendance')
        ");
    }

    public function down()
    {
        DB::statement("
            ALTER TABLE users 
            MODIFY user_type ENUM('admin','employer')
        ");
    }
};
