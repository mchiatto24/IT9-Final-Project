<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Check if the user is authenticated and has the required role
        if (!Auth::check() || !Auth::user()->hasRole($role)) {
            // If the user is not authenticated or doesn't have the role, return 403 (Unauthorized)
            abort(403, 'Unauthorized');
        }

        // If the user has the required role, allow the request to proceed
        return $next($request);
    }
}