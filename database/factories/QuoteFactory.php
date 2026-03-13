<?php

namespace Database\Factories;

use App\Models\QuoteCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'text'        => $this->faker->sentence(12),
            'author'      => $this->faker->name(),
            'category_id' => QuoteCategory::inRandomOrder()->first()?->id
                             ?? QuoteCategory::factory(),
            'is_active'   => $this->faker->boolean(90), // 90% active
        ];
    }
}