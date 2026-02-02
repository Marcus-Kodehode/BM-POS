<?php

/**
 * File: app/Http/Controllers/Admin/PaymentController.php
 * Purpose: Admin operations for managing payments
 * Dependencies: Order, Payment models
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Store a newly created payment
     */
    public function store(StorePaymentRequest $request, Order $order)
    {
        $validated = $request->validated();

        $validated['order_id'] = $order->id;

        Payment::create($validated);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Betaling registrert');
    }

    /**
     * Remove the specified payment
     */
    public function destroy(Order $order, Payment $payment)
    {
        $payment->delete();

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Betaling slettet');
    }
}

/**
 * Summary: Controller for managing payments with soft delete support
 */
