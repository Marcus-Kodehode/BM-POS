<?php

/**
 * File: app/Http/Controllers/Admin/OrderLineController.php
 * Purpose: Admin operations for managing order lines
 * Dependencies: Order, OrderLine, Item models
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderLine;
use Illuminate\Http\Request;

class OrderLineController extends Controller
{
    /**
     * Store a newly created order line
     */
    public function store(Request $request, Order $order)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|integer|min:0',
        ], [
            'item_id.required' => 'Du må velge en vare.',
            'item_id.exists' => 'Valgt vare finnes ikke.',
            'quantity.required' => 'Antall er påkrevd.',
            'quantity.min' => 'Antall må være minst 1.',
            'unit_price.required' => 'Enhetspris er påkrevd.',
            'unit_price.integer' => 'Enhetspris må være et tall (i øre).',
            'unit_price.min' => 'Enhetspris kan ikke være negativ.',
        ]);

        $validated['order_id'] = $order->id;

        // Create order line
        $orderLine = OrderLine::create($validated);

        // Set item status to reserved
        $orderLine->item->update(['status' => 'reserved']);

        // Recalculate order total
        $this->recalculateOrderTotal($order);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Vare lagt til ordre');
    }

    /**
     * Remove the specified order line
     */
    public function destroy(Order $order, OrderLine $orderLine)
    {
        // Revert item status to available
        $orderLine->item->update(['status' => 'available']);

        // Soft delete order line
        $orderLine->delete();

        // Recalculate order total
        $this->recalculateOrderTotal($order);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Vare fjernet fra ordre');
    }

    /**
     * Recalculate order total amount
     */
    private function recalculateOrderTotal(Order $order)
    {
        $total = $order->orderLines()->get()->sum(function ($line) {
            return $line->unit_price * $line->quantity;
        });

        $order->update(['total_amount' => $total]);
    }
}

/**
 * Summary: Controller for managing order lines with automatic item status updates and total recalculation
 */
