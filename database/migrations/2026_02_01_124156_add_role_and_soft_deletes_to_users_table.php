<?php

/**
 * File: database/migrations/2026_02_01_124156_add_role_and_soft_deletes_to_users_table.php
 * Purpose: Add role, password_change_required, and soft deletes to users table
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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->after('password');
            $table->boolean('password_change_required')->default(false)->after('role');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'password_change_required']);
            $table->dropSoftDeletes();
        });
    }
};

/**
 * Summary: Adds role-based access control, password change enforcement, and soft delete capability to users table
 */
