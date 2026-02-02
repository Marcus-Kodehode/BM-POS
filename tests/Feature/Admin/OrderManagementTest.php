<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Item;
use App\Models\OrderLine;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'password_change_required' => false,
        ]);
        
        $this->customer = User::factory()->create([
            'role' => 'customer',
            'password_change_required' => false,
        ]);
    }

    public function test_admin_can_view_order_list(): void
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.orders.index'));

        $response->assertOk();
        $response->assertSee($order->order_number);
        $response->assertSee($this->customer->name);
    }

    public function test_admin_can_filter_orders_by_status(): void
    {
        $openOrder = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'open',
        ]);
        
        $closedOrder = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'closed',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.index', ['status' => 'open']));

        $response->assertOk();
        $response->assertSee($openOrder->order_number);
        $response->assertDontSee($closedOrder->order_number);
    }

    public function test_admin_can_create_order(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.orders.store'), [
            'customer_id' => $this->customer->id,
            'notes' => 'Test order notes',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'customer_id' => $this->customer->id,
            'status' => 'open',
            'notes' => 'Test order notes',
        ]);
    }

    public function test_admin_can_add_order_line(): void
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'open',
        ]);
        
        $item = Item::factory()->create([
            'status' => 'available',
            'target_price' => 50000,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.orders.lines.store', $order), [
                'item_id' => $item->id,
                'quantity' => 2,
                'unit_price' => 50000,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('order_lines', [
            'order_id' => $order->id,
            'item_id' => $item->id,
            'quantity' => 2,
            'unit_price' => 50000,
        ]);
        
        // Verify item status changed to reserved
        $this->assertEquals('reserved', $item->fresh()->status);
    }

    public function test_admin_can_delete_order_line(): void
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'open',
        ]);
        
        $item = Item::factory()->create(['status' => 'reserved']);
        
        $orderLine = OrderLine::create([
            'order_id' => $order->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'unit_price' => 50000,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.orders.lines.destroy', [$order, $orderLine]));

        $response->assertRedirect();
        $this->assertSoftDeleted('order_lines', ['id' => $orderLine->id]);
        
        // Verify item status reverted to available
        $this->assertEquals('available', $item->fresh()->status);
    }

    public function test_admin_can_register_payment(): void
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'open',
            'total_amount' => 100000,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.orders.payments.store', $order), [
                'amount' => 50000,
                'paid_at' => '2026-02-01',
                'payment_method' => 'Vipps',
                'note' => 'First payment',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'amount' => 50000,
            'payment_method' => 'Vipps',
            'note' => 'First payment',
        ]);
    }

    public function test_admin_can_delete_payment(): void
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'open',
        ]);
        
        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 50000,
            'paid_at' => '2026-02-01',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.orders.payments.destroy', [$order, $payment]));

        $response->assertRedirect();
        $this->assertSoftDeleted('payments', ['id' => $payment->id]);
    }

    public function test_admin_can_close_order(): void
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'open',
        ]);
        
        $item1 = Item::factory()->create(['status' => 'reserved']);
        $item2 = Item::factory()->create(['status' => 'reserved']);
        
        OrderLine::create([
            'order_id' => $order->id,
            'item_id' => $item1->id,
            'quantity' => 1,
            'unit_price' => 50000,
        ]);
        
        OrderLine::create([
            'order_id' => $order->id,
            'item_id' => $item2->id,
            'quantity' => 1,
            'unit_price' => 30000,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.orders.close', $order));

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'closed',
        ]);
        
        // Verify all items changed to sold
        $this->assertEquals('sold', $item1->fresh()->status);
        $this->assertEquals('sold', $item2->fresh()->status);
    }

    public function test_admin_can_cancel_order(): void
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'open',
        ]);
        
        $item = Item::factory()->create(['status' => 'reserved']);
        
        $orderLine = OrderLine::create([
            'order_id' => $order->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'unit_price' => 50000,
        ]);
        
        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 25000,
            'paid_at' => '2026-02-01',
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.orders.cancel', $order));

        $response->assertRedirect(route('admin.orders.index'));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);
        
        // Verify items reverted to available
        $this->assertEquals('available', $item->fresh()->status);
        
        // Verify lines and payments soft deleted
        $this->assertSoftDeleted('order_lines', ['id' => $orderLine->id]);
        $this->assertSoftDeleted('payments', ['id' => $payment->id]);
    }

    public function test_outstanding_calculation_is_correct(): void
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'open',
            'total_amount' => 100000,
        ]);
        
        Payment::create([
            'order_id' => $order->id,
            'amount' => 30000,
            'paid_at' => '2026-02-01',
        ]);
        
        Payment::create([
            'order_id' => $order->id,
            'amount' => 20000,
            'paid_at' => '2026-02-02',
        ]);

        $this->assertEquals(50000, $order->outstanding_amount);
    }

    public function test_overpayment_detection_works(): void
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'open',
            'total_amount' => 100000,
        ]);
        
        Payment::create([
            'order_id' => $order->id,
            'amount' => 120000,
            'paid_at' => '2026-02-01',
        ]);

        $this->assertTrue($order->isOverpaid());
        $this->assertEquals(-20000, $order->outstanding_amount);
    }

    public function test_admin_can_manually_override_total(): void
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'open',
            'total_amount' => 100000,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.orders.update-total', $order), [
                'total_amount' => 150000,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'total_amount' => 150000,
        ]);
    }
}
