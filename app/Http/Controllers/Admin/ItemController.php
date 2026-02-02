<?php

/**
 * File: app/Http/Controllers/Admin/ItemController.php
 * Purpose: Admin CRUD operations for inventory/item management
 * Dependencies: Item model
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of items
     */
    public function index(Request $request)
    {
        $query = Item::whereNull('deleted_at');

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $items = $query->orderBy('name')->get();
        $statusFilter = $request->get('status', 'all');

        return view('admin.items.index', compact('items', 'statusFilter'));
    }

    /**
     * Show the form for creating a new item
     */
    public function create()
    {
        return view('admin.items.create');
    }

    /**
     * Store a newly created item
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_price' => 'nullable|integer|min:0',
            'target_price' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Navn er påkrevd.',
            'purchase_price.integer' => 'Innkjøpspris må være et tall (i øre).',
            'purchase_price.min' => 'Innkjøpspris kan ikke være negativ.',
            'target_price.integer' => 'Målpris må være et tall (i øre).',
            'target_price.min' => 'Målpris kan ikke være negativ.',
        ]);

        $validated['status'] = 'available';

        $item = Item::create($validated);

        return redirect()
            ->route('admin.items.index')
            ->with('success', 'Vare opprettet');
    }

    /**
     * Display the specified item
     */
    public function show(Item $item)
    {
        return view('admin.items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified item
     */
    public function edit(Item $item)
    {
        return view('admin.items.edit', compact('item'));
    }

    /**
     * Update the specified item
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_price' => 'nullable|integer|min:0',
            'target_price' => 'nullable|integer|min:0',
            'status' => 'required|in:available,reserved,sold,archived',
        ], [
            'name.required' => 'Navn er påkrevd.',
            'purchase_price.integer' => 'Innkjøpspris må være et tall (i øre).',
            'purchase_price.min' => 'Innkjøpspris kan ikke være negativ.',
            'target_price.integer' => 'Målpris må være et tall (i øre).',
            'target_price.min' => 'Målpris kan ikke være negativ.',
            'status.required' => 'Status er påkrevd.',
            'status.in' => 'Ugyldig status valgt.',
        ]);

        $item->update($validated);

        return redirect()
            ->route('admin.items.index')
            ->with('success', 'Vare oppdatert');
    }

    /**
     * Soft delete the specified item
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()
            ->route('admin.items.index')
            ->with('success', 'Vare slettet');
    }
}

/**
 * Summary: Admin controller for managing inventory items with CRUD operations and status tracking
 */
