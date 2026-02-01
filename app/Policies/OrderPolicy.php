<?php

/**
 * File: app/Policies/OrderPolicy.php
 * Purpose: Authorization policy for Order model
 * Dependencies: User, Order models
 */

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view the model.
     * Admin can view all orders, customer can only view their own
     */
    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() || $user->id === $order->customer_id;
    }

    /**
     * Determine whether the user can update the model.
     * Only admin can update orders
     */
    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     * Only admin can delete orders
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }
}

/**
 * Summary: Order policy enforcing admin-only modifications and customer ownership for viewing
 */
