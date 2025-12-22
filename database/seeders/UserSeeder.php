<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 🔐 ADMIN (UNCHANGED)
        User::create([
            'username'  => 'admin',
            'password'  => Hash::make('admin123'),
            'user_type' => 'admin',
        ]);

        // 👩‍💼 EMPLOYER - Mihiduni
        User::create([
            'username'  => 'mihiduni',
            'password'  => Hash::make('mihiduni123'),
            'user_type' => 'employer',
        ]);

        // 👩‍💼 EMPLOYER - Malshi
        User::create([
            'username'  => 'malshi',
            'password'  => Hash::make('malshi123'),
            'user_type' => 'employer',
        ]);

        // 👨‍💼 EMPLOYER - Avishka
        User::create([
            'username'  => 'avishka',
            'password'  => Hash::make('avishka123'),
            'user_type' => 'employer',
        ]);
    }
}
