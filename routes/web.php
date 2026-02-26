<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesImportController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes (PUBLIC - no middleware)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (require login)
Route::middleware(['auth'])->group(function () {
    
    // Authentication routes
    if (file_exists(base_path('routes/auth.php'))) {
        require base_path('routes/auth.php');
    }

    // Dashboard routes - everyone can view
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
        Route::get('/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
        Route::get('/recent-activities', [DashboardController::class, 'getRecentActivitiesData'])->name('dashboard.recent-activities');
        Route::post('/quick-action', [DashboardController::class, 'quickAction'])->name('dashboard.quick-action');
        Route::post('/export', [DashboardController::class, 'exportData'])->name('dashboard.export');
    });

    // Product routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
        Route::get('/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/', [ProductController::class, 'store'])->name('products.store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::patch('/{product}/update-stock', [ProductController::class, 'updateStock'])->name('products.update-stock');
    });

    // Category routes
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('categories.show');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Supplier routes
    Route::resource('suppliers', SupplierController::class);

    // Inventory routes
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/product/{product}/adjust', [InventoryController::class, 'adjustForm'])->name('adjust.form');
        Route::post('/product/{product}/adjust', [InventoryController::class, 'adjust'])->name('adjust');
        Route::get('/product/{product}/history', [InventoryController::class, 'history'])->name('history');
        Route::post('/product/{product}/quick-add', [InventoryController::class, 'quickAdd'])->name('quick-add');
    });

    // POS routes
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::post('/cart/add', [PosController::class, 'addToCart'])->name('cart.add');
        Route::post('/cart/update', [PosController::class, 'updateCart'])->name('cart.update');
        Route::post('/cart/remove', [PosController::class, 'removeFromCart'])->name('cart.remove');
        Route::post('/cart/clear', [PosController::class, 'clearCart'])->name('cart.clear');
        Route::post('/checkout', [PosController::class, 'store'])->name('checkout');
        Route::get('/receipt/{sale}', [PosController::class, 'receipt'])->name('receipt');
        Route::get('/search', [PosController::class, 'search'])->name('search');
        
        // POS Customer routes
        Route::get('/search-customers', [PosController::class, 'searchCustomers'])->name('search-customers');
        Route::post('/select-customer', [PosController::class, 'selectCustomer'])->name('select-customer');
        Route::post('/clear-customer', [PosController::class, 'clearCustomer'])->name('clear-customer');
        Route::post('/quick-add-customer', [PosController::class, 'quickAddCustomer'])->name('quick-add-customer');
    });

    // Purchase Orders routes
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::post('/purchase-orders/{purchaseOrder}/mark-ordered', [PurchaseOrderController::class, 'markOrdered'])->name('purchase-orders.mark-ordered');
    Route::get('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receiveForm'])->name('purchase-orders.receive-form');
    Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');

    // Sales Reports routes
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::get('/daily', [SaleController::class, 'daily'])->name('daily');
        Route::get('/weekly', [SaleController::class, 'weekly'])->name('weekly');
        Route::get('/weekly/export-csv', [SaleController::class, 'exportWeeklyCsv'])->name('weekly.export-csv');
        Route::get('/monthly', [SaleController::class, 'monthly'])->name('monthly');
        Route::get('/monthly/export-csv', [SaleController::class, 'exportMonthlyCsv'])->name('monthly.export-csv');
        
        // Import routes
        Route::get('/import', [SalesImportController::class, 'showForm'])->name('import.form');
        Route::post('/import/stock', [SalesImportController::class, 'uploadStock'])->name('import.stock');
        Route::post('/import/cost', [SalesImportController::class, 'uploadCost'])->name('import.cost');
        Route::get('/import/analyze', [SalesImportController::class, 'analyze'])->name('import.analyze');
        
        Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
        Route::post('/{sale}/void', [SaleController::class, 'void'])->name('void');
        Route::get('/{sale}/print', [SaleController::class, 'print'])->name('print');
    });

    // Customer routes
    Route::resource('customers', CustomerController::class);
    Route::get('/customers/loyalty/dashboard', [CustomerController::class, 'loyalty'])->name('customers.loyalty');
    Route::get('/api/customers/search', [CustomerController::class, 'search'])->name('customers.search');

    // 👇 USER MANAGEMENT ROUTES (NO MIDDLEWARE - HANDLED IN CONTROLLER)
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::get('/users/{user}/activity', [UserController::class, 'activity'])->name('users.activity');
    Route::get('/activity-logs', [UserController::class, 'allActivity'])->name('users.activity.all');
    Route::get('/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('users.profile.update');
});

// Fallback route
Route::fallback(function () {
    return redirect()->route('login');
});