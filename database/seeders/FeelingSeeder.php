<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeelingSeeder extends Seeder
{
    public function run(): void
    {
        $feelings = [
            ['name' => 'Happy',     'icon' => '😊', 'color' => '#FFD700'],
            ['name' => 'Sad',       'icon' => '😢', 'color' => '#6495ED'],
            ['name' => 'Anxious',   'icon' => '😰', 'color' => '#FF8C00'],
            ['name' => 'Angry',     'icon' => '😠', 'color' => '#FF4500'],
            ['name' => 'Calm',      'icon' => '😌', 'color' => '#90EE90'],
            ['name' => 'Excited',   'icon' => '🤩', 'color' => '#FF69B4'],
            ['name' => 'Tired',     'icon' => '😴', 'color' => '#9370DB'],
            ['name' => 'Grateful',  'icon' => '🙏', 'color' => '#20B2AA'],
            ['name' => 'Lonely',    'icon' => '😔', 'color' => '#708090'],
            ['name' => 'Hopeful',   'icon' => '🌟', 'color' => '#FFA500'],
            ['name' => 'Stressed',  'icon' => '😤', 'color' => '#DC143C'],
            ['name' => 'Confused',  'icon' => '😕', 'color' => '#DAA520'],
        ];

        DB::table('feelings')->insert($feelings);
    }
}