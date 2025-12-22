<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ❌ REMOVE default user factory
        // \App\Models\User::factory(10)->create();

        // ✅ ONLY run your custom UserSeeder
        $this->call([
            UserSeeder::class,
        ]);
    }
}
