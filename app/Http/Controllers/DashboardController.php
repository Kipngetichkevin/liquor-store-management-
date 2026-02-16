<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with real data
     */
    public function index()
    {
        try {
            // 1. BASIC COUNTS
            $totalProducts = Product::count();
            $totalCategories = Category::count();
            $activeProducts = Product::where('status', 'active')->count();
            
            // 2. PRODUCTS BY CATEGORY FOR CHART
            $productsByCategory = Category::withCount('products')->get();
            
            // 3. RECENT ACTIVITIES
            $recentProducts = Product::with('category')
                ->latest()
                ->take(5)
                ->get();
                
            $recentCategories = Category::latest()->take(5)->get();
            
            // 4. TODAY'S SALES - FIXED
            $today = Carbon::today();
            $todaySales = Sale::whereDate('created_at', $today)->count();
            $todayRevenue = Sale::whereDate('created_at', $today)->sum('total_amount');
            
            // 5. WEEKLY SALES TREND (for chart)
            $weeklySales = Sale::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(total_amount) as revenue')
                )
                ->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(Carbon::MONDAY), 
                    Carbon::now()->endOfWeek(Carbon::SUNDAY)
                ])
                ->groupBy('date')
                ->orderBy('date')
                ->get();
            
            // 6. INVENTORY ALERTS
            $lowStockItems = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')
                ->with('category')
                ->limit(5)
                ->get();
            $totalLowStock = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')->count();
            
            // 7. TOP SELLING PRODUCTS - FIXED
            $topProducts = SaleItem::select(
                    'product_id',
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(subtotal) as total_revenue')
                )
                ->with('product')
                ->groupBy('product_id')
                ->orderBy('total_quantity', 'desc')
                ->limit(5)
                ->get();
            
            // 8. PREPARE DATA FOR CHARTS
            $categoryNames = $productsByCategory->pluck('name')->toArray();
            $categoryCounts = $productsByCategory->pluck('products_count')->toArray();
            
            // 9. PREPARE RECENT ACTIVITY LOG
            $activities = $this->getRecentActivities($recentProducts, $recentCategories);
            
            // 10. SET BREADCRUMBS
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
                'todaySales',
                'todayRevenue',
                'weeklySales',
                'lowStockItems',
                'totalLowStock',
                'topProducts',
                'categoryNames',
                'categoryCounts',
                'activities',
                'breadcrumbs'
            ));
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Dashboard Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return dashboard with empty data
            return view('pages.dashboard.index', [
                'totalProducts' => 0,
                'totalCategories' => 0,
                'activeProducts' => 0,
                'todaySales' => 0,
                'todayRevenue' => 0,
                'totalLowStock' => 0,
                'categoryNames' => [],
                'categoryCounts' => [],
                'activities' => [],
                'weeklySales' => [],
                'topProducts' => [],
                'breadcrumbs' => [['title' => 'Dashboard', 'url' => route('dashboard')]]
            ]);
        }
    }
    
    /**
     * Get recent activities for the dashboard
     */
    private function getRecentActivities($recentProducts, $recentCategories)
    {
        $activities = [];
        
        // Add recent products
        foreach ($recentProducts as $product) {
            $activities[] = [
                'type' => 'product',
                'action' => 'added',
                'title' => $product->name,
                'subtitle' => $product->category->name ?? 'Uncategorized',
                'time' => $product->created_at->diffForHumans(),
                'icon' => 'fas fa-wine-bottle',
                'color' => 'text-blue-600 bg-blue-100'
            ];
        }
        
        // Add recent categories
        foreach ($recentCategories as $category) {
            $activities[] = [
                'type' => 'category',
                'action' => 'created',
                'title' => $category->name,
                'subtitle' => 'Category',
                'time' => $category->created_at->diffForHumans(),
                'icon' => 'fas fa-tag',
                'color' => 'text-green-600 bg-green-100'
            ];
        }
        
        // Sort by time (newest first)
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        return array_slice($activities, 0, 8);
    }
    
    /**
     * Get real-time stats for AJAX refresh
     */
    public function getStats()
    {
        try {
            $today = Carbon::today();
            
            $stats = [
                'totalProducts' => Product::count(),
                'totalCategories' => Category::count(),
                'activeProducts' => Product::where('status', 'active')->count(),
                'todaySales' => Sale::whereDate('created_at', $today)->count(),
                'todayRevenue' => Sale::whereDate('created_at', $today)->sum('total_amount'),
                'lowStockItems' => Product::whereColumn('stock_quantity', '<=', 'min_stock_level')->count(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Dashboard Stats Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'data' => [
                    'totalProducts' => 0,
                    'totalCategories' => 0,
                    'activeProducts' => 0,
                    'todaySales' => 0,
                    'todayRevenue' => 0,
                    'lowStockItems' => 0,
                ]
            ]);
        }
    }
    
    /**
     * Get chart data for AJAX
     */
    public function getChartData()
    {
        try {
            // Products by Category
            $productsByCategory = Category::withCount('products')->get();
            
            // Weekly Sales
            $weeklySales = Sale::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total_amount) as revenue')
                )
                ->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(Carbon::MONDAY), 
                    Carbon::now()->endOfWeek(Carbon::SUNDAY)
                ])
                ->groupBy('date')
                ->orderBy('date')
                ->get();
            
            // Top Products
            $topProducts = SaleItem::select(
                    'product_id',
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(subtotal) as total_revenue')
                )
                ->with('product')
                ->groupBy('product_id')
                ->orderBy('total_quantity', 'desc')
                ->limit(5)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'categories' => [
                        'labels' => $productsByCategory->pluck('name'),
                        'data' => $productsByCategory->pluck('products_count')
                    ],
                    'weeklySales' => $weeklySales,
                    'topProducts' => $topProducts
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Chart Data Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'data' => []
            ]);
        }
    }
}