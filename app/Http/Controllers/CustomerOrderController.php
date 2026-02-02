<?php

/**
 * File: app/Http/Controllers/CustomerOrderController.php
 * Purpose: Customer order viewing (read-only)
 * Dependencies: Order model, OrderPolicy
 */

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CustomerOrderController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of customer's orders
     */
    public function index(Request $request): View
    {
        $customer = $request->user();
        
        // Get all non-cancelled orders
        $orders = $customer->orders()
            ->where('status', '!=', 'cancelled')
            ->with(['orderLines', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                $order->outstanding = $order->outstanding_amount;
                return $order;
            });
        
        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Display the specified order
     */
    public function show(Order $order): View
    {
        // Authorization check via policy
        $this->authorize('view', $order);
        
        $order->load(['orderLines.item', 'payments']);
        
        $outstanding = $order->outstanding_amount;
        
        return view('customer.orders.show', compact('order', 'outstanding'));
    }
}

/**
 * Summary: Customer order controller with read-only access and policy authorization
 */

