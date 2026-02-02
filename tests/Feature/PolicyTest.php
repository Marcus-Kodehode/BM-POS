<?php

/**
 * File: tests/Feature/PolicyTest.php
 * Purpose: Test authorization policies for data access
 * Dependencies: User, Order models, OrderPolicy
 */

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test customer cannot view another customer's order
     */
    public function test_customer_cannot_view_another_customers_order(): void
    {
        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);

        $order = Order::factory()->create([
            'customer_id' => $customer1->id,
        ]);

        $this->actingAs($customer2);

        $response = $this->get(route('orders.show', $order));

        $response->assertStatus(403);
    }

    /**
     * Test customer can view their own order
     */
    public function test_customer_can_view_own_order(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $this->actingAs($customer);

        $response = $this->get(route('orders.show', $order));

        $response->assertStatus(200);
        $response->assertSee($order->order_number);
    }

    /**
     * Test admin can view all orders
     */
    public function test_admin_can_view_all_orders(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $this->actingAs($admin);

        // Admin can view via admin route
        $response = $this->get(route('admin.orders.show', $order));
        $response->assertStatus(200);
        $response->assertSee($order->order_number);

        // Admin can also view via customer route (policy allows)
        $response = $this->get(route('orders.show', $order));
        $response->assertStatus(200);
    }

    /**
     * Test guest cannot view any order
     */
    public function test_guest_cannot_view_orders(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $response = $this->get(route('orders.show', $order));

        $response->assertRedirect('/login');
    }

    /**
     * Test customer cannot access admin order routes
     */
    public function test_customer_cannot_access_admin_order_routes(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $this->actingAs($customer);

        // Customer cannot access admin order routes even for their own orders
        $this->get(route('admin.orders.show', $order))->assertStatus(403);
        $this->get(route('admin.orders.index'))->assertStatus(403);
        $this->get(route('admin.orders.create'))->assertStatus(403);
    }
}

/**
 * Summary: Tests for OrderPolicy and data access authorization
 */
