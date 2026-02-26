<?php
namespace App\Traits;

trait RoleCheckTrait
{
    /**
     * Check if the current user has one of the allowed roles
     * 
     * @param array|string $allowedRoles
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    protected function checkRole($allowedRoles)
    {
        // Convert single role to array
        if (is_string($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }
        
        // Check if user is logged in
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        
        // Check if user has any of the allowed roles
        if (in_array($user->role, $allowedRoles)) {
            return true;
        }
        
        // User doesn't have permission
        return redirect()->route('dashboard')
            ->with('error', 'You do not have permission to access this page.');
    }
    
    /**
     * Check if current user is admin
     */
    protected function isAdmin()
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }
    
    /**
     * Check if current user is manager
     */
    protected function isManager()
    {
        return auth()->check() && auth()->user()->role === 'manager';
    }
    
    /**
     * Check if current user is cashier
     */
    protected function isCashier()
    {
        return auth()->check() && auth()->user()->role === 'cashier';
    }
    
    /**
     * Check if current user is stock keeper
     */
    protected function isStockKeeper()
    {
        return auth()->check() && auth()->user()->role === 'stock_keeper';
    }
}
