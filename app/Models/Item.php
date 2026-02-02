<?php

/**
 * File: app/Models/Item.php
 * Purpose: Item model for inventory management
 * Dependencies: SoftDeletes trait, OrderLine model
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name',
        'description',
        'purchase_price',
        'target_price',
        'status',
    ];
    
    protected $casts = [
        'purchase_price' => 'integer',
        'target_price' => 'integer',
    ];
    
    /**
     * Get all order lines for this item
     */
    public function orderLines()
    {
        return $this->hasMany(OrderLine::class);
    }
    
    /**
     * Check if item is available for sale
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}

/**
 * Summary: Item model with soft deletes, price tracking, and status management
 */
