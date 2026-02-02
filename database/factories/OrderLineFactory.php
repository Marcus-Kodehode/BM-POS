<?php

/**
 * File: database/factories/OrderLineFactory.php
 * Purpose: Factory for generating test OrderLine instances
 * Dependencies: OrderLine, Order, Item models
 */

namespace Database\Factories;

use App\Models\Order;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderLine>
 */
class OrderLineFactory extends Factory
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
            'item_id' => Item::factory(),
            'unit_price' => fake()->numberBetween(10000, 500000), // 100 kr to 5000 kr
            'quantity' => fake()->numberBetween(1, 3),
        ];
    }
}

/**
 * Summary: Factory for creating test order lines with random prices and quantities
 */
