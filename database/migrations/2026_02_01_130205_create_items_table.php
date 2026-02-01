<?php

/**
 * File: database/migrations/2026_02_01_130205_create_items_table.php
 * Purpose: Create items table for inventory management
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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('purchase_price')->nullable()->comment('Price in øre');
            $table->integer('target_price')->nullable()->comment('Price in øre');
            $table->string('status')->default('available'); // available, reserved, sold, archived
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};

/**
 * Summary: Creates items table with soft deletes for inventory tracking
 */
