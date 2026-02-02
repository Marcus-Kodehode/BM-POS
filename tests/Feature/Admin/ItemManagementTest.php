<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_item_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $item = Item::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.items.index'));

        $response->assertStatus(200);
        $response->assertSee($item->name);
    }

    public function test_admin_can_filter_items_by_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $availableItem = Item::factory()->create(['status' => 'available']);
        $soldItem = Item::factory()->create(['status' => 'sold']);

        $response = $this->actingAs($admin)->get(route('admin.items.index', ['status' => 'available']));

        $response->assertStatus(200);
        $response->assertSee($availableItem->name);
        $response->assertDontSee($soldItem->name);
    }

    public function test_admin_can_create_item(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.items.store'), [
            'name' => 'Test Item',
            'description' => 'Test description',
            'purchase_price' => 10000,
            'target_price' => 15000,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('items', [
            'name' => 'Test Item',
            'status' => 'available',
            'purchase_price' => 10000,
            'target_price' => 15000,
        ]);
    }

    public function test_admin_can_update_item(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $item = Item::factory()->create();

        $response = $this->actingAs($admin)->put(route('admin.items.update', $item), [
            'name' => 'Updated Name',
            'description' => $item->description,
            'purchase_price' => $item->purchase_price,
            'target_price' => $item->target_price,
            'status' => 'sold',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Updated Name',
            'status' => 'sold',
        ]);
    }

    public function test_admin_can_soft_delete_item(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $item = Item::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.items.destroy', $item));

        $response->assertRedirect();
        $this->assertSoftDeleted('items', ['id' => $item->id]);
    }

    public function test_customer_cannot_access_admin_item_routes(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get(route('admin.items.index'));

        $response->assertStatus(403);
    }
}
