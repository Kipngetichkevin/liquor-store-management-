<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryController; // 👈 ADD THIS

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes (if any)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication routes (if using Laravel Breeze/Jetstream)
if (file_exists(base_path('routes/auth.php'))) {
    require base_path('routes/auth.php');
}

// Dashboard routes
Route::prefix('dashboard')->group(function () {
    // Main dashboard page
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboard API endpoints
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
    
    // Quick stock update
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

// 👇 INVENTORY MANAGEMENT ROUTES (ADDED HERE)
Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('index');
    Route::get('/product/{product}/adjust', [InventoryController::class, 'adjustForm'])->name('adjust.form');
    Route::post('/product/{product}/adjust', [InventoryController::class, 'adjust'])->name('adjust');
    Route::get('/product/{product}/history', [InventoryController::class, 'history'])->name('history');
    Route::post('/product/{product}/quick-add', [InventoryController::class, 'quickAdd'])->name('quick-add');
});

// Fallback route
Route::fallback(function () {
    return redirect()->route('dashboard');
});