<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. BASIC COUNTS – these always work
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        
        // 2. ACTIVE PRODUCTS – only if 'status' column exists
        try {
            $activeProducts = Product::where('status', 'active')->count();
        } catch (\Exception $e) {
            $activeProducts = $totalProducts; // fallback
        }

        // 3. PRODUCTS BY CATEGORY – with safety check
        try {
            $productsByCategory = Category::withCount('products')->get();
            $categoryNames = $productsByCategory->pluck('name')->toArray();
            $categoryCounts = $productsByCategory->pluck('products_count')->toArray();
        } catch (\Exception $e) {
            $productsByCategory = collect();
            $categoryNames = [];
            $categoryCounts = [];
        }

        // 4. RECENT ACTIVITIES
        try {
            $recentProducts = Product::with('category')->latest()->take(5)->get();
            $recentCategories = Category::latest()->take(5)->get();
            $activities = $this->getRecentActivities($recentProducts, $recentCategories);
        } catch (\Exception $e) {
            $recentProducts = collect();
            $recentCategories = collect();
            $activities = [];
        }

        // 5. PLACEHOLDERS (zero for now)
        $todaySales = 0;
        $todayRevenue = 0;
        $lowStockItems = [];
        $totalLowStock = 0;
        $weeklySalesTrend = [];

        // 6. BREADCRUMBS
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')]
        ];

        return view('pages.dashboard.index', compact(
            'totalProducts',
            'totalCategories',
            'activeProducts',
            'productsByCategory',
            'recentProducts',
            'recentCategories',
            'categoryNames',
            'categoryCounts',
            'activities',
            'todaySales',
            'todayRevenue',
            'lowStockItems',
            'totalLowStock',
            'weeklySalesTrend',
            'breadcrumbs'
        ));
    }

    private function getRecentActivities($recentProducts, $recentCategories)
    {
        $activities = [];

        foreach ($recentProducts as $product) {
            $activities[] = [
                'type' => 'product',
                'action' => 'added',
                'title' => $product->name,
                'subtitle' => $product->category->name ?? 'Uncategorized',
                'time' => $product->created_at->diffForHumans(),
                'icon' => 'fas fa-wine-bottle',
                'color' => 'text-blue-600 bg-blue-100',
            ];
        }

        foreach ($recentCategories as $category) {
            $activities[] = [
                'type' => 'category',
                'action' => 'created',
                'title' => $category->name,
                'subtitle' => 'Category',
                'time' => $category->created_at->diffForHumans(),
                'icon' => 'fas fa-tag',
                'color' => 'text-green-600 bg-green-100',
            ];
        }

        // Sort newest first
        usort($activities, fn($a, $b) => strtotime($b['time']) - strtotime($a['time']));

        return array_slice($activities, 0, 8);
    }

    public function getStats()
    {
        try {
            $stats = [
                'totalProducts' => Product::count(),
                'totalCategories' => Category::count(),
                'activeProducts' => Product::where('status', 'active')->count(),
                'todaySales' => 0,
                'todayRevenue' => 0,
                'lowStockItems' => 0,
            ];
        } catch (\Exception $e) {
            $stats = [
                'totalProducts' => Product::count(),
                'totalCategories' => Category::count(),
                'activeProducts' => Product::count(),
                'todaySales' => 0,
                'todayRevenue' => 0,
                'lowStockItems' => 0,
            ];
        }

        return response()->json($stats);
    }
}