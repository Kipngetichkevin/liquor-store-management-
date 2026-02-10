<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

// Categories Routes
Route::resource('categories', CategoryController::class);

// Products Routes - MAKE SURE THIS LINE IS HERE
Route::resource('products', ProductController::class);

// Test routes
Route::get('/test-data', function () {
    return response()->json(['message' => 'Test data works!']);
});

Route::get('/test-view', function () {
    return view('test');
});