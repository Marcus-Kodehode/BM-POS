<?php

/**
 * File: database/factories/PaymentFactory.php
 * Purpose: Factory for generating test Payment instances
 * Dependencies: Payment, Order models
 */

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'amount' => fake()->numberBetween(10000, 500000), // 100 kr to 5000 kr
            'paid_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'payment_method' => fake()->randomElement(['Vipps', 'Bank', 'Kontant', null]),
            'note' => fake()->optional()->sentence(),
        ];
    }
}

/**
 * Summary: Factory for creating test payments with random amounts and methods
 */
