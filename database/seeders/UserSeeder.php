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
        UserSettings::create(['user_id' => $admin->id]);

        // Fixed demo user
        $demo = User::create([
            'email'    => 'demo@moodtracker.com',
            'password' => Hash::make('Password1'),
            'name'     => 'Demo User',
            'avatar'   => null,
            'role_id'  => 2, // user
            'status'   => 'active',
        ]);
        UserSettings::create(['user_id' => $demo->id]);

        // 10 random active users with settings
        User::factory()->count(10)->active()->create()->each(function ($user) {
            UserSettings::factory()->create(['user_id' => $user->id]);
        });

        // 3 pending users (awaiting approval)
        User::factory()->count(3)->pending()->create();

        // 2 blocked users
        User::factory()->count(2)->blocked()->create();
    }
}