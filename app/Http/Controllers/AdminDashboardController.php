<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Calculate total outstanding across all open orders
        $totalOutstanding = Order::where('status', 'open')
            ->get()
            ->sum(function ($order) {
                return $order->outstanding_amount;
            });

        // Count open orders
        $openOrdersCount = Order::where('status', 'open')->count();

        // Count active customers (non-deleted, role = customer)
        $activeCustomersCount = User::where('role', 'customer')
            ->whereNull('deleted_at')
            ->count();

        // Count items by status
        $itemsByStatus = Item::select('status', DB::raw('count(*) as count'))
            ->whereNull('deleted_at')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get top 10 customers by outstanding (desc)
        $topCustomers = User::where('role', 'customer')
            ->whereNull('deleted_at')
            ->withCount(['orders as total_outstanding' => function ($query) {
                $query->where('status', 'open');
            }])
            ->get()
            ->map(function ($customer) {
                $outstanding = $customer->orders()
                    ->where('status', 'open')
                    ->get()
                    ->sum(function ($order) {
                        return $order->outstanding_amount;
                    });
                
                $customer->outstanding = $outstanding;
                return $customer;
            })
            ->sortByDesc('outstanding')
            ->take(10);

        return view('admin.dashboard', compact(
            'totalOutstanding',
            'openOrdersCount',
            'activeCustomersCount',
            'itemsByStatus',
            'topCustomers'
        ));
    }
}
