<?php

namespace Database\Seeders;

use App\Models\Feeling;
use App\Models\MoodEntry;
use App\Models\User;
use Illuminate\Database\Seeder;

class MoodEntrySeeder extends Seeder
{
    public function run(): void
    {
        $feelings = Feeling::all();

        // Give active users entries for the last 30 days
        User::where('status', 'active')->get()->each(function ($user) use ($feelings) {
            $usedDates = [];

            for ($i = 0; $i < 20; $i++) {
                // Pick a unique random date in last 30 days
                do {
                    $date = now()->subDays(rand(0, 30))->format('Y-m-d');
                } while (in_array($date, $usedDates));

                $usedDates[] = $date;

                $entry = MoodEntry::create([
                    'user_id'     => $user->id,
                    'mood_level'  => rand(1, 10),
                    'sleep_hours' => round(mt_rand(30, 100) / 10, 1), // 3.0 - 10.0
                    'reflection'  => rand(0, 10) > 3 ? fake()->sentence(rand(5, 30)) : null,
                    'entry_date'  => $date,
                ]);

                // Attach 1-3 random feelings to each entry
                $entry->feelings()->attach(
                    $feelings->random(rand(1, 3))->pluck('id')->toArray()
                );
            }
        });
    }
}