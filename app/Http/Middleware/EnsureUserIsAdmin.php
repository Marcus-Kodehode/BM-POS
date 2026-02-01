<?php

/**
 * File: app/Http/Middleware/EnsureUserIsAdmin.php
 * Purpose: Middleware to restrict access to admin-only routes
 * Dependencies: User model with isAdmin() method
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        return $next($request);
    }
}

/**
 * Summary: Blocks non-admin users from accessing admin routes with 403 Forbidden response
 */
