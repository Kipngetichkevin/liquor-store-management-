<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function terminate($request, $response)
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $route = $request->route();
                
                if ($route) {
                    $routeName = $route->getName();
                    $method = $request->method();
                    
                    // Skip logging for GET requests and specific routes
                    if ($method === 'GET' || in_array($routeName, ['dashboard', 'login', 'logout'])) {
                        return;
                    }
                    
                    $module = explode('.', $routeName)[0] ?? 'unknown';
                    $action = $this->getActionFromMethod($method, $routeName);
                    
                    // Get description based on route
                    $description = $this->getDescription($routeName, $request);
                    
                    // Log the activity if the method exists
                    if (method_exists($user, 'logActivity')) {
                        $user->logActivity(
                            $action,
                            $module,
                            $description,
                            null, // old values (would need to capture before)
                            null  // new values (would need to capture after)
                        );
                    }
                }
            }
        } catch (\Exception $e) {
            // Log error but don't break the application
            Log::error('Activity logging failed: ' . $e->getMessage());
        }
    }

    /**
     * Get action from HTTP method and route name.
     *
     * @param string $method
     * @param string $routeName
     * @return string
     */
    private function getActionFromMethod($method, $routeName)
    {
        if ($method === 'POST' && str_contains($routeName, 'store')) {
            return 'create';
        } elseif (($method === 'PUT' || $method === 'PATCH') && str_contains($routeName, 'update')) {
            return 'update';
        } elseif ($method === 'DELETE' && str_contains($routeName, 'destroy')) {
            return 'delete';
        } elseif (str_contains($routeName, 'login')) {
            return 'login';
        } elseif (str_contains($routeName, 'logout')) {
            return 'logout';
        } elseif ($method === 'POST') {
            return 'create';
        } elseif ($method === 'PUT' || $method === 'PATCH') {
            return 'update';
        } elseif ($method === 'DELETE') {
            return 'delete';
        }
        
        return strtolower($method);
    }

    /**
     * Get description for the activity.
     *
     * @param string $routeName
     * @param Request $request
     * @return string
     */
    private function getDescription($routeName, $request)
    {
        $input = $request->except(['_token', '_method', 'password', 'password_confirmation']);
        
        $descriptions = [
            'products.store' => 'Created new product: ' . ($input['name'] ?? ''),
            'products.update' => 'Updated product: ' . ($input['name'] ?? ''),
            'products.destroy' => 'Deleted product ID: ' . $request->route('product'),
            
            'customers.store' => 'Created new customer: ' . ($input['name'] ?? ''),
            'customers.update' => 'Updated customer: ' . ($input['name'] ?? ''),
            'customers.destroy' => 'Deleted customer ID: ' . $request->route('customer'),
            
            'suppliers.store' => 'Created new supplier: ' . ($input['name'] ?? ''),
            'suppliers.update' => 'Updated supplier: ' . ($input['name'] ?? ''),
            'suppliers.destroy' => 'Deleted supplier ID: ' . $request->route('supplier'),
            
            'pos.checkout' => 'Processed sale: KSh ' . ($input['amount_paid'] ?? '0'),
            'inventory.adjust' => 'Adjusted inventory for product ID: ' . ($input['product_id'] ?? ''),
        ];
        
        return $descriptions[$routeName] ?? "Performed {$routeName}";
    }
}