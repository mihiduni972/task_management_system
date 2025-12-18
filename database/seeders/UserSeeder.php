<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ADMIN USER
        User::create([
            'username'  => 'admin',
            'password'  => Hash::make('admin123'),
            'user_type' => 'admin',
        ]);

        // EMPLOYER USER
        User::create([
            'username'  => 'employer',
            'password'  => Hash::make('emp123'),
            'user_type' => 'employer',
        ]);
    }
}
