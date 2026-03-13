<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MoodEntryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'mood_level'  => $this->faker->numberBetween(1, 10),
            'sleep_hours' => $this->faker->randomFloat(1, 3, 10), // realistic sleep range
            'reflection'  => $this->faker->optional(0.7)->sentence(
                $this->faker->numberBetween(5, 40)
            ), // 70% chance of having a reflection
            'entry_date'  => $this->faker->unique()->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
        ];
    }
}