<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $module
     * @param  string  $action
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $module, $action = 'view')
    {
        // Check if user is logged in
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Admin can do everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if user has permission for this module/action
        if (method_exists($user, 'canAccess') && $user->canAccess($module, $action)) {
            return $next($request);
        }

        // Deny access
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthorized. Insufficient permissions.'], 403);
        }

        return redirect()->route('dashboard')
            ->with('error', 'You do not have permission to access this page.');
    }
}