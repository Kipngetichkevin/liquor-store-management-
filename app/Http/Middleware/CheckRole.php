<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user account is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }

        // Check if user has any of the required roles
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if ($user->role === $role) {
                    return $next($request);
                }
            }

            // If we get here, user doesn't have any required role
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized. Insufficient permissions.'], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        // Check for module and action permissions based on route name
        $routeName = $request->route()->getName();
        if ($routeName) {
            $parts = explode('.', $routeName);
            $module = $parts[0] ?? null;
            $action = $parts[1] ?? 'view';

            if ($module && method_exists($user, 'canAccess') && $user->canAccess($module, $action)) {
                return $next($request);
            }
        }

        // Default: allow access
        return $next($request);
    }
}