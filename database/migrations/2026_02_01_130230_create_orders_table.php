<?php

/**
 * File: database/migrations/2026_02_01_130230_create_orders_table.php
 * Purpose: Create orders table for sales tracking
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Disable transactions for this migration (Neon compatibility)
     */
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('open'); // open, closed, cancelled
            $table->integer('total_amount')->default(0)->comment('Total in øre');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

/**
 * Summary: Creates orders table with soft deletes and foreign key to users
 */
