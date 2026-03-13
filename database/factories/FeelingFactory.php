<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FeelingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'  => $this->faker->unique()->word(),
            'icon'  => 'emoji_' . $this->faker->word(),
            'color' => $this->faker->hexColor(),
        ];
    }
}