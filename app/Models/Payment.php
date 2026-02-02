<?php

/**
 * File: app/Models/Payment.php
 * Purpose: Payment model for tracking partial payments against orders
 * Dependencies: SoftDeletes trait, Order model
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'order_id',
        'amount',
        'paid_at',
        'payment_method',
        'note',
    ];
    
    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'date',
    ];
    
    /**
     * Get the order for this payment
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

/**
 * Summary: Payment model with soft deletes and date casting
 */
