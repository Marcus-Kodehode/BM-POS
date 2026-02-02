<?php

/**
 * File: tests/Feature/AuthTest.php
 * Purpose: Test authentication and authorization flows
 * Dependencies: User model, middleware
 */

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin redirects to /admin after login
     */
    public function test_admin_redirects_to_admin_dashboard_after_login(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * Test customer redirects to /dashboard after login
     */
    public function test_customer_redirects_to_dashboard_after_login(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $response = $this->post('/login', [
            'email' => $customer->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($customer);
    }

    /**
     * Test customer cannot access admin routes
     */
    public function test_customer_cannot_access_admin_routes(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($customer);

        // Test various admin routes
        $this->get('/admin')->assertStatus(403);
        $this->get('/admin/customers')->assertStatus(403);
        $this->get('/admin/items')->assertStatus(403);
        $this->get('/admin/orders')->assertStatus(403);
    }

    /**
     * Test guest is redirected to login on protected routes
     */
    public function test_guest_redirected_to_login_on_protected_routes(): void
    {
        // Customer routes
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/orders')->assertRedirect('/login');
        $this->get('/profile')->assertRedirect('/login');

        // Admin routes
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/admin/customers')->assertRedirect('/login');
        $this->get('/admin/items')->assertRedirect('/login');
        $this->get('/admin/orders')->assertRedirect('/login');
    }

    /**
     * Test admin can access admin routes
     */
    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $this->get('/admin')->assertStatus(200);
        $this->get('/admin/customers')->assertStatus(200);
        $this->get('/admin/items')->assertStatus(200);
        $this->get('/admin/orders')->assertStatus(200);
    }

    /**
     * Test customer can access customer routes
     */
    public function test_customer_can_access_customer_routes(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($customer);

        $this->get('/dashboard')->assertStatus(200);
        $this->get('/orders')->assertStatus(200);
        $this->get('/profile')->assertStatus(200);
    }
}

/**
 * Summary: Tests for authentication flows and route authorization
 */
