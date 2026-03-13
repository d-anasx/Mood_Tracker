<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title'   => $this->faker->randomElement([
                'Daily Reminder',
                'Don\'t forget your check-in!',
                'How are you feeling today?',
                'Time for your mood log',
            ]),
            'message'  => $this->faker->sentence(),
            'is_read'  => $this->faker->boolean(40),
            'sent_at'  => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
