<?php

/**
 * File: app/Http/Middleware/EnsurePasswordChanged.php
 * Purpose: Middleware to enforce password change for users with temporary passwords
 * Dependencies: User model with password_change_required field
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->password_change_required) {
            if (!$request->routeIs('profile.edit', 'profile.update', 'logout')) {
                return redirect()->route('profile.edit')
                    ->with('warning', 'Du må endre passordet ditt før du kan fortsette.');
            }
        }
        
        return $next($request);
    }
}

/**
 * Summary: Redirects users with temporary passwords to profile edit page until they change their password
 */
