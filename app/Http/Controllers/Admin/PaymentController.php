<?php

/**
 * File: app/Http/Controllers/Admin/PaymentController.php
 * Purpose: Admin operations for managing payments
 * Dependencies: Order, Payment models
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Store a newly created payment
     */
    public function store(Request $request, Order $order)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
            'paid_at' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ], [
            'amount.required' => 'Beløp er påkrevd.',
            'amount.integer' => 'Beløp må være et tall (i øre).',
            'amount.min' => 'Beløp må være minst 1 øre.',
            'paid_at.required' => 'Betalingsdato er påkrevd.',
            'paid_at.date' => 'Betalingsdato må være en gyldig dato.',
        ]);

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
