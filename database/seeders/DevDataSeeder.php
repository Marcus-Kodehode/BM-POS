<?php

/**
 * File: database/seeders/DevDataSeeder.php
 * Purpose: Seed realistic test data for development and demos
 * Dependencies: User, Item, Order, OrderLine, Payment models
 */

namespace Database\Seeders;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test customers
        $customer1 = User::create([
            'name' => 'Ola Nordmann',
            'email' => 'ola@example.com',
            'phone' => '12345678',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'password_change_required' => false,
        ]);

        $customer2 = User::create([
            'name' => 'Kari Hansen',
            'email' => 'kari@example.com',
            'phone' => '87654321',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'password_change_required' => false,
        ]);

        $customer3 = User::create([
            'name' => 'Per Olsen',
            'email' => null,
            'phone' => '55512345',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'password_change_required' => true,
        ]);

        // Create test items
        $item1 = Item::create([
            'name' => 'Vintage Stol',
            'description' => 'Klassisk trestol fra 1960-tallet',
            'purchase_price' => 50000, // 500 kr
            'target_price' => 120000, // 1200 kr
            'status' => 'available',
        ]);

        $item2 = Item::create([
            'name' => 'Antikk Bord',
            'description' => 'Solid eik spisebord',
            'purchase_price' => 200000, // 2000 kr
            'target_price' => 450000, // 4500 kr
            'status' => 'available',
        ]);

        $item3 = Item::create([
            'name' => 'Lampe',
            'description' => 'Art Deco bordlampe',
            'purchase_price' => 30000, // 300 kr
            'target_price' => 75000, // 750 kr
            'status' => 'available',
        ]);

        $item4 = Item::create([
            'name' => 'Bokhylle',
            'description' => 'Høy bokhylle i mahogni',
            'purchase_price' => 150000, // 1500 kr
            'target_price' => 350000, // 3500 kr
            'status' => 'available',
        ]);

        $item5 = Item::create([
            'name' => 'Speil',
            'description' => 'Stort veggspeil med gullramme',
            'purchase_price' => 80000, // 800 kr
            'target_price' => 180000, // 1800 kr
            'status' => 'available',
        ]);

        $item6 = Item::create([
            'name' => 'Kommode',
            'description' => 'Vintage kommode med 5 skuffer',
            'purchase_price' => 120000, // 1200 kr
            'target_price' => 280000, // 2800 kr
            'status' => 'available',
        ]);

        // Create order 1 - Open with partial payment
        $order1 = Order::create([
            'customer_id' => $customer1->id,
            'status' => 'open',
            'total_amount' => 0,
            'notes' => 'Kunde ønsker levering neste uke',
        ]);

        OrderLine::create([
            'order_id' => $order1->id,
            'item_id' => $item1->id,
            'unit_price' => 120000,
            'quantity' => 1,
        ]);

        OrderLine::create([
            'order_id' => $order1->id,
            'item_id' => $item3->id,
            'unit_price' => 75000,
            'quantity' => 1,
        ]);

        $item1->update(['status' => 'reserved']);
        $item3->update(['status' => 'reserved']);
        $order1->update(['total_amount' => 195000]); // 1950 kr

        Payment::create([
            'order_id' => $order1->id,
            'amount' => 100000, // 1000 kr
            'paid_at' => now()->subDays(3),
            'payment_method' => 'Vipps',
            'note' => 'Første avdrag',
        ]);

        // Create order 2 - Closed (fully paid)
        $order2 = Order::create([
            'customer_id' => $customer2->id,
            'status' => 'open',
            'total_amount' => 0,
            'notes' => null,
        ]);

        OrderLine::create([
            'order_id' => $order2->id,
            'item_id' => $item2->id,
            'unit_price' => 450000,
            'quantity' => 1,
        ]);

        $item2->update(['status' => 'reserved']);
        $order2->update(['total_amount' => 450000]); // 4500 kr

        Payment::create([
            'order_id' => $order2->id,
            'amount' => 200000, // 2000 kr
            'paid_at' => now()->subDays(10),
            'payment_method' => 'Bank',
            'note' => 'Første betaling',
        ]);

        Payment::create([
            'order_id' => $order2->id,
            'amount' => 250000, // 2500 kr
            'paid_at' => now()->subDays(2),
            'payment_method' => 'Bank',
            'note' => 'Restbetaling',
        ]);

        // Close this order
        $order2->update(['status' => 'closed']);
        $item2->update(['status' => 'sold']);

        // Create order 3 - Open with no payments
        $order3 = Order::create([
            'customer_id' => $customer1->id,
            'status' => 'open',
            'total_amount' => 0,
            'notes' => 'Kunde vurderer flere varer',
        ]);

        OrderLine::create([
            'order_id' => $order3->id,
            'item_id' => $item5->id,
            'unit_price' => 180000,
            'quantity' => 1,
        ]);

        $item5->update(['status' => 'reserved']);
        $order3->update(['total_amount' => 180000]); // 1800 kr

        // Create order 4 - Open with multiple items and overpayment
        $order4 = Order::create([
            'customer_id' => $customer3->id,
            'status' => 'open',
            'total_amount' => 0,
            'notes' => null,
        ]);

        OrderLine::create([
            'order_id' => $order4->id,
            'item_id' => $item4->id,
            'unit_price' => 350000,
            'quantity' => 1,
        ]);

        OrderLine::create([
            'order_id' => $order4->id,
            'item_id' => $item6->id,
            'unit_price' => 280000,
            'quantity' => 1,
        ]);

        $item4->update(['status' => 'reserved']);
        $item6->update(['status' => 'reserved']);
        
        // Manual override - discount applied
        $order4->update(['total_amount' => 550000]); // 5500 kr (discount from 6300 kr)

        Payment::create([
            'order_id' => $order4->id,
            'amount' => 300000, // 3000 kr
            'paid_at' => now()->subDays(5),
            'payment_method' => 'Kontant',
            'note' => null,
        ]);

        Payment::create([
            'order_id' => $order4->id,
            'amount' => 300000, // 3000 kr
            'paid_at' => now()->subDays(1),
            'payment_method' => 'Kontant',
            'note' => 'Siste betaling',
        ]);

        // This order is now overpaid (paid 6000 kr, total 5500 kr)

        $this->command->info('✅ Dev data seeded successfully!');
        $this->command->info('');
        $this->command->info('Test Customers:');
        $this->command->info('  - Ola Nordmann (ola@example.com / 12345678) - password: password');
        $this->command->info('  - Kari Hansen (kari@example.com / 87654321) - password: password');
        $this->command->info('  - Per Olsen (55512345) - password: password (must change)');
        $this->command->info('');
        $this->command->info('Orders:');
        $this->command->info('  - Order 1: Ola - 2 items, partial payment (950 kr outstanding)');
        $this->command->info('  - Order 2: Kari - 1 item, closed (fully paid)');
        $this->command->info('  - Order 3: Ola - 1 item, no payments (1800 kr outstanding)');
        $this->command->info('  - Order 4: Per - 2 items, overpaid by 500 kr');
    }
}

/**
 * Summary: Seeder for creating realistic test data including customers, items, orders, and payments
 */
