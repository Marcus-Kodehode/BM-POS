<?php

/**
 * File: app/Models/Order.php
 * Purpose: Order model for sales tracking with auto-generated order numbers
 * Dependencies: SoftDeletes trait, User, OrderLine, Payment models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'order_number',
        'customer_id',
        'status',
        'total_amount',
        'notes',
    ];
    
    protected $casts = [
        'total_amount' => 'integer',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }
    
    /**
     * Get the customer for this order
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    
    /**
     * Get all order lines for this order
     */
    public function orderLines()
    {
        return $this->hasMany(OrderLine::class);
    }
    
    /**
     * Get all payments for this order
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    
    /**
     * Get total paid amount
     */
    public function getPaidAmountAttribute(): int
    {
        return $this->payments()->sum('amount');
    }
    
    /**
     * Get outstanding amount
     */
    public function getOutstandingAmountAttribute(): int
    {
        return $this->total_amount - $this->paid_amount;
    }
    
    /**
     * Check if order is overpaid
     */
    public function isOverpaid(): bool
    {
        return $this->outstanding_amount < 0;
    }
    
    /**
     * Generate unique order number (YYYY-NNN format)
     */
    private static function generateOrderNumber(): string
    {
        $year = date('Y');
        $lastOrder = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastOrder ? ((int) substr($lastOrder->order_number, -3)) + 1 : 1;
        
        return $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}

/**
 * Summary: Order model with soft deletes, auto-generated order numbers, and outstanding balance calculations
 */
