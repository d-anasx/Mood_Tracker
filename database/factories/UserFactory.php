<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email'     => $this->faker->unique()->safeEmail(),
            'password'  => Hash::make('Password1'), // meets: 8 chars, upper, lower, digit
            'name'      => $this->faker->name(),
            'avatar'    => $this->faker->imageUrl(100, 100, 'people'),
            'role_id'   => Role::where('name', 'user')->first()?->id ?? 2,
            'status'    => $this->faker->randomElement(['pending', 'active', 'active', 'active', 'blocked']),
            // weighted: more active users than pending/blocked
        ];
    }

    // Reusable states
    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function blocked(): static
    {
        return $this->state(['status' => 'blocked']);
    }

    public function admin(): static
    {
        return $this->state([
            'role_id' => Role::where('name', 'admin')->first()?->id ?? 1,
            'status'  => 'active',
        ]);
    }
}