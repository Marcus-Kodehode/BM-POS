<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Item;
use App\Models\OrderLine;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPortalTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $otherCustomer;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customer = User::factory()->create([
            'role' => 'customer',
            'password_change_required' => false,
        ]);
        
        $this->otherCustomer = User::factory()->create([
            'role' => 'customer',
            'password_change_required' => false,
        ]);
    }

    public function test_customer_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->customer)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Min Oversikt');
    }

    public function test_dashboard_displays_correct_totals(): void
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

        $response = $this->actingAs($this->customer)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('1 000,00 kr'); // Total purchased
        $response->assertSee('300,00 kr');   // Total paid
        $response->assertSee('700,00 kr');   // Outstanding
    }

    public function test_dashboard_excludes_cancelled_orders(): void
    {
        Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'cancelled',
            'total_amount' => 50000,
        ]);
        
        $openOrder = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'open',
            'total_amount' => 100000,
        ]);

        $response = $this->actingAs($this->customer)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('1 000,00 kr'); // Only open order
        $response->assertDontSee('500,00 kr'); // Cancelled order excluded
    }

    public function test_customer_can_view_orders_list(): void
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->customer)->get(route('orders.index'));

        $response->assertOk();
        $response->assertSee($order->order_number);
    }

    public function test_customer_can_view_own_order_detail(): void
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'open',
            'total_amount' => 100000,
        ]);
        
        $item = Item::factory()->create();
        OrderLine::create([
            'order_id' => $order->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'unit_price' => 100000,
        ]);

        $response = $this->actingAs($this->customer)->get(route('orders.show', $order));

        $response->assertOk();
        $response->assertSee($order->order_number);
        $response->assertSee($item->name);
    }

    public function test_customer_cannot_view_other_customer_order(): void
    {
        $order = Order::factory()->create([
            'customer_id' => $this->otherCustomer->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->customer)->get(route('orders.show', $order));

        $response->assertForbidden();
    }

    public function test_customer_cannot_access_admin_routes(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_password_change_alert_shown_when_required(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'password_change_required' => true,
        ]);

        $response = $this->actingAs($customer)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSee('Passordendring påkrevd');
    }

    public function test_password_change_clears_requirement_flag(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'password_change_required' => true,
        ]);

        $response = $this->actingAs($customer)->patch(route('profile.update'), [
            'name' => $customer->name,
            'email' => $customer->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $this->assertFalse($customer->fresh()->password_change_required);
    }

    public function test_account_deletion_shows_outstanding_balance_warning(): void
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'open',
            'total_amount' => 100000,
        ]);

        $response = $this->actingAs($this->customer)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSee('Utestående beløp');
        $response->assertSee('1 000,00 kr');
    }
}
