<?php

/**
 * File: app/Http/Controllers/Admin/CustomerController.php
 * Purpose: Admin CRUD operations for customer management
 * Dependencies: User model, Illuminate\Support\Str for password generation
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * Display a listing of active customers
     */
    public function index()
    {
        $customers = User::where('role', 'customer')
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
            ->sortBy('name');

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created customer
     */
    public function store(StoreCustomerRequest $request)
    {
        $validated = $request->validated();

        // Generate secure random temporary password
        $tempPassword = Str::random(12);

        $customer = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($tempPassword),
            'role' => 'customer',
            'password_change_required' => true,
        ]);

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Kunde opprettet')
            ->with('temp_password', $tempPassword);
    }

    /**
     * Display the specified customer
     */
    public function show(User $customer)
    {
        // Calculate totals
        $totalPurchased = $customer->orders()
            ->whereIn('status', ['open', 'closed'])
            ->sum('total_amount');

        $totalPaid = $customer->orders()
            ->whereIn('status', ['open', 'closed'])
            ->get()
            ->sum(function ($order) {
                return $order->paid_amount;
            });

        $outstanding = $totalPurchased - $totalPaid;

        // Get all orders with their details
        $orders = $customer->orders()
            ->with(['orderLines', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                $order->outstanding = $order->outstanding_amount;
                return $order;
            });

        return view('admin.customers.show', compact(
            'customer',
            'totalPurchased',
            'totalPaid',
            'outstanding',
            'orders'
        ));
    }

    /**
     * Show the form for editing the specified customer
     */
    public function edit(User $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer
     */
    public function update(UpdateCustomerRequest $request, User $customer)
    {
        $validated = $request->validated();

        $customer->update($validated);

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Kunde oppdatert');
    }

    /**
     * Soft delete the specified customer
     */
    public function destroy(User $customer)
    {
        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Kunde slettet');
    }

    /**
     * Display soft-deleted customers
     */
    public function deleted()
    {
        $customers = User::where('role', 'customer')
            ->onlyTrashed()
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
            ->sortBy('name');

        return view('admin.customers.deleted', compact('customers'));
    }

    /**
     * Restore a soft-deleted customer
     */
    public function restore($id)
    {
        $customer = User::onlyTrashed()->findOrFail($id);
        $customer->restore();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Kunde gjenopprettet');
    }

    /**
     * Permanently delete a customer
     */
    public function forceDestroy($id)
    {
        $customer = User::onlyTrashed()->findOrFail($id);
        $customer->forceDelete();

        return redirect()
            ->route('admin.customers.deleted')
            ->with('success', 'Kunde permanent slettet');
    }
}

/**
 * Summary: Admin controller for managing customers with CRUD operations, soft deletes, and restoration
 */
