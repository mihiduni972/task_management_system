<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username'  => 'admin',
            'password'  => Hash::make('admin123'),
            'user_type' => 'admin',
        ]);

        User::create([
            'username'  => 'employer',
            'password'  => Hash::make('employer123'),
            'user_type' => 'employer',
        ]);
    }
}
