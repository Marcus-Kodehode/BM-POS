<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_customer_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($admin)->get(route('admin.customers.index'));

        $response->assertStatus(200);
        $response->assertSee($customer->name);
        $response->assertSee($customer->email);
    }

    public function test_admin_can_create_customer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.customers.store'), [
            'name' => 'Test Customer',
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'role' => 'customer',
            'password_change_required' => true,
        ]);
    }

    public function test_admin_can_view_customer_detail(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($admin)->get(route('admin.customers.show', $customer));

        $response->assertStatus(200);
        $response->assertSee($customer->name);
        $response->assertSee($customer->email);
    }

    public function test_admin_can_update_customer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($admin)->put(route('admin.customers.update', $customer), [
            'name' => 'Updated Name',
            'email' => $customer->email,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_admin_can_soft_delete_customer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($admin)->delete(route('admin.customers.destroy', $customer));

        $response->assertRedirect();
        $this->assertSoftDeleted('users', ['id' => $customer->id]);
    }

    public function test_admin_can_view_deleted_customers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        $customer->delete();

        $response = $this->actingAs($admin)->get(route('admin.customers.deleted'));

        $response->assertStatus(200);
        $response->assertSee($customer->name);
    }

    public function test_admin_can_restore_customer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        $customer->delete();

        $response = $this->actingAs($admin)->post(route('admin.customers.restore', $customer->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'deleted_at' => null,
        ]);
    }

    public function test_customer_cannot_access_admin_customer_routes(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get(route('admin.customers.index'));

        $response->assertStatus(403);
    }
}
