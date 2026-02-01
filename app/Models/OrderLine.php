<?php

/**
 * File: app/Models/OrderLine.php
 * Purpose: OrderLine model to link items to orders with quantity and pricing
 * Dependencies: SoftDeletes trait, Order, Item models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderLine extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'order_id',
        'item_id',
        'unit_price',
        'quantity',
    ];
    
    protected $casts = [
        'unit_price' => 'integer',
        'quantity' => 'integer',
    ];
    
    /**
     * Get the order for this line
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Get the item for this line
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    
    /**
     * Get total for this line (unit_price * quantity)
     */
    public function getTotalAttribute(): int
    {
        return $this->unit_price * $this->quantity;
    }
}

/**
 * Summary: OrderLine model with soft deletes and total calculation
 */
