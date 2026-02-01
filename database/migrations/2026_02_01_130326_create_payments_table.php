<?php

/**
 * File: database/migrations/2026_02_01_130326_create_payments_table.php
 * Purpose: Create payments table for tracking partial payments against orders
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->integer('amount')->comment('Amount in øre');
            $table->date('paid_at');
            $table->string('payment_method')->nullable();
            $table->string('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

/**
 * Summary: Creates payments table with soft deletes and foreign key to orders
 */
