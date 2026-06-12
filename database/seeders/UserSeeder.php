<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active',
            'timezone' => 'UTC',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'username' => 'demo-user',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
            'timezone' => 'America/New_York',
            'email_verified_at' => now(),
        ]);

        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => "User $i",
                'email' => "user$i@example.com",
                'username' => "user$i",
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'timezone' => 'UTC',
                'email_verified_at' => now(),
            ]);
        }
    }
}
