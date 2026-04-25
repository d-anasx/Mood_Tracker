<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Fixed admin account
        $admin = User::create([
            'email'    => 'admin@moodtracker.com',
            'password' => Hash::make('Admin1234'),
            'name'     => 'Super Admin',
            'avatar'   => null,
            'role_id'  => 1, // admin
            'status'   => 'active',
        ]);
    }
}