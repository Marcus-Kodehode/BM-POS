<?php

/**
 * File: app/Http/Controllers/CustomerDashboardController.php
 * Purpose: Customer dashboard with order overview and balance tracking
 * Dependencies: Order model
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    /**
     * Display customer dashboard
     */
    public function index(Request $request): View
    {
        $customer = $request->user();
        
        // Get all non-cancelled orders for this customer
        $orders = $customer->orders()
            ->where('status', '!=', 'cancelled')
            ->with(['orderLines', 'payments'])
            ->get();
        
        // Calculate totals
        $totalPurchased = $orders->sum('total_amount');
        $totalPaid = $orders->sum(function ($order) {
            return $order->paid_amount;
        });
        $outstanding = $totalPurchased - $totalPaid;
        
        // Get open orders only for the table
        $openOrders = $orders->where('status', 'open')->map(function ($order) {
            $order->outstanding = $order->outstanding_amount;
            return $order;
        });
        
        return view('customer.dashboard', compact(
            'totalPurchased',
            'totalPaid',
            'outstanding',
            'openOrders'
        ));
    }
}

/**
 * Summary: Customer dashboard controller with balance calculations and open orders display
 */

