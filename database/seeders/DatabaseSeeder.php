<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,          // 1. roles first (users depend on it)
            FeelingSeeder::class,       // 2. feelings (standalone)
            QuoteCategorySeeder::class, // 3. quote categories (quotes depend on it)
            QuoteSeeder::class,         // 4. quotes
            UserSeeder::class,          // 5. users + user_settings
            MoodEntrySeeder::class,     // 6. mood entries + mood_entry_feelings
            AdminActionSeeder::class,   // 7. admin actions (users must exist)
        ]);
    }
}