<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => null, // Will be auto-generated
            'customer_id' => User::factory()->create(['role' => 'customer']),
            'status' => 'open',
            'total_amount' => fake()->numberBetween(10000, 500000), // 100 kr to 5000 kr
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
