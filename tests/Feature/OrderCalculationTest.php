<?php

/**
 * File: tests/Feature/OrderCalculationTest.php
 * Purpose: Test order calculations (outstanding, overpayment, totals)
 * Dependencies: Order, OrderLine, Payment, Item models
 */

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Payment;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCalculationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test outstanding balance calculation
     */
    public function test_outstanding_balance_calculation(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'total_amount' => 100000, // 1000 kr
        ]);

        // No payments - outstanding should equal total
        $this->assertEquals(100000, $order->outstanding_amount);

        // Add partial payment
        Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 40000, // 400 kr
        ]);

        $order->refresh();
        $this->assertEquals(60000, $order->outstanding_amount); // 600 kr remaining

        // Add another payment
        Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 30000, // 300 kr
        ]);

        $order->refresh();
        $this->assertEquals(30000, $order->outstanding_amount); // 300 kr remaining
    }

    /**
     * Test overpayment detection
     */
    public function test_overpayment_detection(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'total_amount' => 100000, // 1000 kr
        ]);

        // Not overpaid initially
        $this->assertFalse($order->isOverpaid());

        // Add payment equal to total
        Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 100000,
        ]);

        $order->refresh();
        $this->assertFalse($order->isOverpaid());
        $this->assertEquals(0, $order->outstanding_amount);

        // Add extra payment - now overpaid
        Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 10000, // 100 kr extra
        ]);

        $order->refresh();
        $this->assertTrue($order->isOverpaid());
        $this->assertEquals(-10000, $order->outstanding_amount); // Negative = overpaid
    }

    /**
     * Test total_amount recalculation when adding order line
     */
    public function test_total_recalculation_on_order_line_add(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'total_amount' => 0,
        ]);

        $item1 = Item::factory()->create([
            'status' => 'available',
            'target_price' => 50000, // 500 kr
        ]);

        $item2 = Item::factory()->create([
            'status' => 'available',
            'target_price' => 75000, // 750 kr
        ]);

        $this->actingAs($admin);

        // Add first order line
        $this->post(route('admin.orders.lines.store', $order), [
            'item_id' => $item1->id,
            'quantity' => 1,
            'unit_price' => 50000,
        ]);

        $order->refresh();
        $this->assertEquals(50000, $order->total_amount);

        // Add second order line
        $this->post(route('admin.orders.lines.store', $order), [
            'item_id' => $item2->id,
            'quantity' => 2, // Quantity of 2
            'unit_price' => 75000,
        ]);

        $order->refresh();
        $this->assertEquals(200000, $order->total_amount); // 50000 + (75000 * 2)
    }

    /**
     * Test total_amount recalculation when deleting order line
     */
    public function test_total_recalculation_on_order_line_delete(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'total_amount' => 0,
        ]);

        $item1 = Item::factory()->create(['status' => 'available']);
        $item2 = Item::factory()->create(['status' => 'available']);

        $line1 = OrderLine::factory()->create([
            'order_id' => $order->id,
            'item_id' => $item1->id,
            'unit_price' => 50000,
            'quantity' => 1,
        ]);

        $line2 = OrderLine::factory()->create([
            'order_id' => $order->id,
            'item_id' => $item2->id,
            'unit_price' => 75000,
            'quantity' => 1,
        ]);

        $item1->update(['status' => 'reserved']);
        $item2->update(['status' => 'reserved']);
        $order->update(['total_amount' => 125000]); // 1250 kr

        $this->actingAs($admin);

        // Delete one order line
        $this->delete(route('admin.orders.lines.destroy', [$order, $line1]));

        $order->refresh();
        $this->assertEquals(75000, $order->total_amount); // Only line2 remains
    }

    /**
     * Test paid_amount accessor
     */
    public function test_paid_amount_accessor(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'total_amount' => 100000,
        ]);

        // No payments
        $this->assertEquals(0, $order->paid_amount);

        // Add payments
        Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 30000,
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 20000,
        ]);

        $order->refresh();
        $this->assertEquals(50000, $order->paid_amount);
    }

    /**
     * Test manual total override doesn't break calculations
     */
    public function test_manual_total_override(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'total_amount' => 0,
        ]);

        $item = Item::factory()->create(['status' => 'available']);

        OrderLine::factory()->create([
            'order_id' => $order->id,
            'item_id' => $item->id,
            'unit_price' => 100000, // 1000 kr
            'quantity' => 1,
        ]);

        $order->update(['total_amount' => 100000]);

        // Add payment
        Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 50000,
        ]);

        $order->refresh();
        $this->assertEquals(50000, $order->outstanding_amount);

        // Admin manually overrides total (discount)
        $this->actingAs($admin);
        $this->patch(route('admin.orders.update-total', $order), [
            'total_amount' => 80000, // Discounted to 800 kr
        ]);

        $order->refresh();
        $this->assertEquals(80000, $order->total_amount);
        $this->assertEquals(30000, $order->outstanding_amount); // 800 - 500 = 300
    }

    /**
     * Test quantity affects total calculation
     */
    public function test_quantity_affects_total_calculation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'total_amount' => 0,
        ]);

        $item = Item::factory()->create(['status' => 'available']);

        $this->actingAs($admin);

        // Add order line with quantity 3
        $this->post(route('admin.orders.lines.store', $order), [
            'item_id' => $item->id,
            'quantity' => 3,
            'unit_price' => 25000, // 250 kr each
        ]);

        $order->refresh();
        $this->assertEquals(75000, $order->total_amount); // 250 * 3 = 750 kr
    }
}

/**
 * Summary: Tests for order financial calculations including outstanding, overpayment, and totals
 */
