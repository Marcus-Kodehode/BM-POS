<?php

/**
 * File: app/Http/Controllers/Admin/OrderController.php
 * Purpose: Admin CRUD operations for order management
 * Dependencies: Order, User, Item, OrderLine, Payment models
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\User;
use App\Models\Item;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'orderLines.item', 'payments']);

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->get()->map(function ($order) {
            $order->outstanding = $order->outstanding_amount;
            return $order;
        });

        $statusFilter = $request->get('status', 'all');

        return view('admin.orders.index', compact('orders', 'statusFilter'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        $customers = User::where('role', 'customer')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return view('admin.orders.create', compact('customers'));
    }

    /**
     * Store a newly created order
     */
    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();

        $validated['status'] = 'open';
        $validated['total_amount'] = 0;

        $order = Order::create($validated);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Ordre opprettet');
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'orderLines.item', 'payments']);

        // Calculate totals
        $autoCalculatedTotal = $order->orderLines->sum(function ($line) {
            return $line->unit_price * $line->quantity;
        });

        $outstanding = $order->outstanding_amount;
        $isOverpaid = $order->isOverpaid();

        // Get available items for adding to order
        $availableItems = Item::where('status', 'available')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return view('admin.orders.show', compact(
            'order',
            'autoCalculatedTotal',
            'outstanding',
            'isOverpaid',
            'availableItems'
        ));
    }

    /**
     * Close an order
     */
    public function close(Order $order)
    {
        $order->update(['status' => 'closed']);

        // Set all items in order to sold
        foreach ($order->orderLines as $line) {
            $line->item->update(['status' => 'sold']);
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Ordre lukket');
    }

    /**
     * Cancel an order
     */
    public function cancel(Order $order)
    {
        $order->update(['status' => 'cancelled']);

        // Soft delete all order lines and revert items to available
        foreach ($order->orderLines as $line) {
            $line->item->update(['status' => 'available']);
            $line->delete();
        }

        // Soft delete all payments
        foreach ($order->payments as $payment) {
            $payment->delete();
        }

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Ordre kansellert');
    }

    /**
     * Update order total amount (manual override)
     */
    public function updateTotal(Request $request, Order $order)
    {
        $validated = $request->validate([
            'total_amount' => 'required|integer|min:0',
        ], [
            'total_amount.required' => 'Totalbeløp er påkrevd.',
            'total_amount.integer' => 'Totalbeløp må være et tall (i øre).',
            'total_amount.min' => 'Totalbeløp kan ikke være negativt.',
        ]);

        $order->update($validated);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Totalbeløp oppdatert');
    }
}

/**
 * Summary: Admin controller for managing orders with CRUD operations, payments, and status management
 */
