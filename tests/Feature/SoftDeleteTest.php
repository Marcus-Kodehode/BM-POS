<?php

/**
 * File: tests/Feature/SoftDeleteTest.php
 * Purpose: Test soft delete functionality across all models
 * Dependencies: User, Item, Order, OrderLine, Payment models
 */

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test soft-deleted customers are excluded from queries
     */
    public function test_soft_deleted_customers_excluded_from_queries(): void
    {
        $customer1 = User::factory()->create(['role' => 'customer', 'name' => 'Active Customer']);
        $customer2 = User::factory()->create(['role' => 'customer', 'name' => 'Deleted Customer']);

        // Soft delete customer2
        $customer2->delete();

        // Query should only return active customer
        $customers = User::where('role', 'customer')->get();
        
        $this->assertCount(1, $customers);
        $this->assertEquals('Active Customer', $customers->first()->name);
        $this->assertNull($customer1->deleted_at);
        $this->assertNotNull($customer2->fresh()->deleted_at);
    }

    /**
     * Test soft-deleted items are excluded from queries
     */
    public function test_soft_deleted_items_excluded_from_queries(): void
    {
        $item1 = Item::factory()->create(['name' => 'Active Item']);
        $item2 = Item::factory()->create(['name' => 'Deleted Item']);

        // Soft delete item2
        $item2->delete();

        // Query should only return active item
        $items = Item::all();
        
        $this->assertCount(1, $items);
        $this->assertEquals('Active Item', $items->first()->name);
    }

    /**
     * Test customer restore functionality
     */
    public function test_customer_restore_functionality(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($admin);

        // Soft delete customer
        $this->delete(route('admin.customers.destroy', $customer));

        $customer->refresh();
        $this->assertNotNull($customer->deleted_at);

        // Restore customer
        $this->post(route('admin.customers.restore', $customer->id));

        $customer->refresh();
        $this->assertNull($customer->deleted_at);

        // Customer should appear in active list
        $response = $this->get(route('admin.customers.index'));
        $response->assertSee($customer->name);
    }

    /**
     * Test customer permanent delete
     */
    public function test_customer_permanent_delete(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        $customerId = $customer->id;

        $this->actingAs($admin);

        // Soft delete first
        $this->delete(route('admin.customers.destroy', $customer));

        $customer->refresh();
        $this->assertNotNull($customer->deleted_at);

        // Permanent delete
        $this->delete(route('admin.customers.forceDestroy', $customer->id));

        // Customer should not exist at all
        $this->assertNull(User::withTrashed()->find($customerId));
    }

    /**
     * Test soft-deleted order lines excluded from order total
     */
    public function test_soft_deleted_order_lines_excluded_from_total(): void
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
            'unit_price' => 30000,
            'quantity' => 1,
        ]);

        $item1->update(['status' => 'reserved']);
        $item2->update(['status' => 'reserved']);
        $order->update(['total_amount' => 80000]);

        // Soft delete one line
        $line1->delete();

        // Order should only count non-deleted lines
        $activeLines = $order->orderLines()->get();
        $this->assertCount(1, $activeLines);
        $this->assertEquals($line2->id, $activeLines->first()->id);
    }

    /**
     * Test soft-deleted payments excluded from paid amount
     */
    public function test_soft_deleted_payments_excluded_from_paid_amount(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'total_amount' => 100000,
        ]);

        $payment1 = Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 40000,
        ]);

        $payment2 = Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 30000,
        ]);

        $order->refresh();
        $this->assertEquals(70000, $order->paid_amount);

        // Soft delete one payment
        $payment1->delete();

        $order->refresh();
        $this->assertEquals(30000, $order->paid_amount); // Only payment2 counts
        $this->assertEquals(70000, $order->outstanding_amount);
    }

    /**
     * Test cancelled order soft deletes lines and payments
     */
    public function test_cancelled_order_soft_deletes_lines_and_payments(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'open',
            'total_amount' => 0,
        ]);

        $item = Item::factory()->create(['status' => 'available']);

        $line = OrderLine::factory()->create([
            'order_id' => $order->id,
            'item_id' => $item->id,
            'unit_price' => 50000,
            'quantity' => 1,
        ]);

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 25000,
        ]);

        $item->update(['status' => 'reserved']);
        $order->update(['total_amount' => 50000]);

        $this->actingAs($admin);

        // Cancel order
        $this->patch(route('admin.orders.cancel', $order));

        $order->refresh();
        $line->refresh();
        $payment->refresh();
        $item->refresh();

        // Order should be cancelled
        $this->assertEquals('cancelled', $order->status);

        // Line and payment should be soft deleted
        $this->assertNotNull($line->deleted_at);
        $this->assertNotNull($payment->deleted_at);

        // Item should be available again
        $this->assertEquals('available', $item->status);

        // Active queries should not include deleted records
        $this->assertCount(0, $order->orderLines()->get());
        $this->assertCount(0, $order->payments()->get());
    }

    /**
     * Test soft-deleted records can be queried with withTrashed
     */
    public function test_soft_deleted_records_queryable_with_trashed(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $item = Item::factory()->create();

        $customer->delete();
        $item->delete();

        // Normal queries don't find them
        $this->assertCount(0, User::where('role', 'customer')->get());
        $this->assertCount(0, Item::all());

        // withTrashed finds them
        $this->assertCount(1, User::withTrashed()->where('role', 'customer')->get());
        $this->assertCount(1, Item::withTrashed()->get());

        // onlyTrashed finds only deleted
        $this->assertCount(1, User::onlyTrashed()->where('role', 'customer')->get());
        $this->assertCount(1, Item::onlyTrashed()->get());
    }

    /**
     * Test admin can view deleted customers page
     */
    public function test_admin_can_view_deleted_customers_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer', 'name' => 'Deleted Customer']);

        $customer->delete();

        $this->actingAs($admin);

        $response = $this->get(route('admin.customers.deleted'));

        $response->assertStatus(200);
        $response->assertSee('Deleted Customer');
    }

    /**
     * Test customer cannot be permanently deleted without soft delete first
     */
    public function test_permanent_delete_requires_soft_delete_first(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($admin);

        // Try to permanently delete without soft delete first
        $response = $this->delete(route('admin.customers.forceDestroy', $customer->id));

        // Should fail (404) because route expects onlyTrashed
        $response->assertStatus(404);

        // Customer should still exist
        $this->assertNotNull(User::find($customer->id));
    }
}

/**
 * Summary: Tests for soft delete functionality including exclusion, restore, and permanent delete
 */
