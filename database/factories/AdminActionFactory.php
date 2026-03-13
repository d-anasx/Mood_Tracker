<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminActionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'admin_id'       => User::factory()->admin(),
            'target_user_id' => User::factory()->active(),
            'action_type'    => $this->faker->randomElement(['approve', 'block', 'unblock', 'delete']),
            'reason'         => $this->faker->optional(0.6)->sentence(),
        ];
    }
}