<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuoteCategorySeeder extends Seeder
{
    public function run(): void
    {
        // mood_level corresponds to mood ranges (1-10)
        $categories = [
            ['name' => 'Very Low',    'mood_level' => 1],
            ['name' => 'Low',         'mood_level' => 2],
            ['name' => 'Below Average','mood_level' => 3],
            ['name' => 'Neutral Low', 'mood_level' => 4],
            ['name' => 'Neutral',     'mood_level' => 5],
            ['name' => 'Neutral High','mood_level' => 6],
            ['name' => 'Good',        'mood_level' => 7],
            ['name' => 'Great',       'mood_level' => 8],
            ['name' => 'Excellent',   'mood_level' => 9],
            ['name' => 'Outstanding', 'mood_level' => 10],
        ];

        DB::table('quote_categories')->insert($categories);
    }
}