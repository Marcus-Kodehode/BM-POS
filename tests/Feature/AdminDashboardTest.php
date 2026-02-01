<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Total utestående');
        $response->assertSee('Åpne ordrer');
        $response->assertSee('Antall kunder');
    }

    public function test_dashboard_displays_correct_metrics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        
        // Create an open order with outstanding balance
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'open',
            'total_amount' => 100000, // 1000,00 kr
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('1 000,00 kr'); // Total outstanding
        $response->assertSee('1'); // Open orders count
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }
}
