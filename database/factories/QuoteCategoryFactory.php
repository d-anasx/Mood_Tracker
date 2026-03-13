<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class QuoteCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => $this->faker->unique()->word(),
            'mood_level' => $this->faker->numberBetween(1, 10),
        ];
    }
}