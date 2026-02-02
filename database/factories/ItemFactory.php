<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'purchase_price' => fake()->numberBetween(5000, 50000), // 50 kr to 500 kr
            'target_price' => fake()->numberBetween(10000, 100000), // 100 kr to 1000 kr
            'status' => fake()->randomElement(['available', 'reserved', 'sold', 'archived']),
        ];
    }
}
