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
    public function index()
    {
        try {
            $totalProducts = Product::count();
            $totalCategories = Category::count();
            $activeProducts = Product::where('status', 'active')->count();
            
            $productsByCategory = Category::withCount('products')->get();
            
            $recentProducts = Product::with('category')->latest()->take(5)->get();
            $recentCategories = Category::latest()->take(5)->get();
            
            $today = Carbon::today();
            $todaySales = Sale::whereDate('created_at', $today)->count();
            $todayRevenue = Sale::whereDate('created_at', $today)->sum('total_amount');
            
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
            
            $lowStockItems = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')
                ->with('category')
                ->limit(5)
                ->get();
            $totalLowStock = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')->count();
            
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
            
            $categoryNames = $productsByCategory->pluck('name')->toArray();
            $categoryCounts = $productsByCategory->pluck('products_count')->toArray();
            
            $activities = $this->getRecentActivities($recentProducts, $recentCategories);
            
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
            \Log::error('Dashboard Error: ' . $e->getMessage());
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
                'color' => 'text-blue-600 bg-blue-100'
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
                'color' => 'text-green-600 bg-green-100'
            ];
        }
        
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        return array_slice($activities, 0, 8);
    }

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

    public function getChartData()
    {
        try {
            $productsByCategory = Category::withCount('products')->get();
            
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