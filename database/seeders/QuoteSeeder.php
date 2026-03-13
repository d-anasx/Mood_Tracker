<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $quotes = [
            // Low mood (categories 1-3)
            ['text' => 'Even the darkest night will end and the sun will rise.', 'author' => 'Victor Hugo',       'category_id' => 1, 'is_active' => true],
            ['text' => 'You are stronger than you think.',                        'author' => 'A.A. Milne',        'category_id' => 2, 'is_active' => true],
            ['text' => 'This too shall pass.',                                    'author' => 'Persian Proverb',   'category_id' => 2, 'is_active' => true],
            ['text' => 'Every storm runs out of rain.',                           'author' => 'Maya Angelou',      'category_id' => 3, 'is_active' => true],
            // Neutral mood (categories 4-6)
            ['text' => 'Take it one day at a time.',                              'author' => 'Unknown',           'category_id' => 4, 'is_active' => true],
            ['text' => 'Small steps every day lead to big changes.',              'author' => 'Unknown',           'category_id' => 5, 'is_active' => true],
            ['text' => 'Progress, not perfection.',                               'author' => 'Unknown',           'category_id' => 6, 'is_active' => true],
            // Good mood (categories 7-8)
            ['text' => 'Keep going, you are doing great!',                        'author' => 'Unknown',           'category_id' => 7, 'is_active' => true],
            ['text' => 'Happiness is a direction, not a place.',                  'author' => 'Sydney J. Harris',  'category_id' => 8, 'is_active' => true],
            // Excellent mood (categories 9-10)
            ['text' => 'You are on fire — keep that energy!',                     'author' => 'Unknown',           'category_id' => 9,  'is_active' => true],
            ['text' => 'Today is your day. Make it count.',                       'author' => 'Unknown',           'category_id' => 10, 'is_active' => true],
        ];

        DB::table('quotes')->insert($quotes);
    }
}