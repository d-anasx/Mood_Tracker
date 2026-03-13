<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserSettingsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'reminder_time'    => $this->faker->time('H:i:s'),
            'reminder_enabled' => $this->faker->boolean(60),
            'timezone'         => $this->faker->timezone(),
        ];
    }
}
