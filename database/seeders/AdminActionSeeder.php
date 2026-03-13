<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminActionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@moodtracker.com')->first();
        $users = User::where('role_id', 2)->get();

        foreach ($users->take(5) as $user) {
            DB::table('admin_actions')->insert([
                'admin_id'       => $admin->id,
                'target_user_id' => $user->id,
                'action_type'    => 'approve',
                'reason'         => null,
                'created_at'     => now()->subDays(rand(1, 30)),
            ]);
        }

        // Log a block action
        $blocked = User::where('status', 'blocked')->first();
        if ($blocked) {
            DB::table('admin_actions')->insert([
                'admin_id'       => $admin->id,
                'target_user_id' => $blocked->id,
                'action_type'    => 'block',
                'reason'         => 'Violation of terms of service.',
                'created_at'     => now()->subDays(5),
            ]);
        }
    }
}