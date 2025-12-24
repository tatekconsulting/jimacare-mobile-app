<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        // Check if user is authenticated
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Check if user has a role
        if (!$request->user()->role) {
            abort(403, 'Access denied. Your account does not have a valid role.');
        }

        // Check if user is admin
        $userRole = $request->user()->role->slug ?? '';
        if ($userRole === 'admin') {
            return $next($request);
        }

        // Return 403 Forbidden instead of 404 Not Found for better security
        abort(403, 'Access denied. Admin privileges required.');
    }
}
