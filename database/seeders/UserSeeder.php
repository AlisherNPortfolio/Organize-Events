<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Creator User',
            'email' => 'creator@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CREATOR,
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Fined User',
            'email' => 'fined@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_USER,
            'status' => 'active',
            'fine_until' => now()->addDays(3),
        ]);

        User::factory()->count(5)->state([
            'role' => User::ROLE_CREATOR
        ])->create();

        User::factory()->count(15)->state([
            'role' => User::ROLE_USER
        ])->create();
    }
}
