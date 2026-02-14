<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereColumn('stock_quantity', '<=', 'min_stock_level');
            } elseif ($request->stock_status === 'out') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($request->stock_status === 'in') {
                $query->where('stock_quantity', '>', 0);
            }
        }

        $products = $query->orderBy('stock_quantity', 'asc')->paginate(15);

        $totalProducts = Product::count();
        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')->count();
        $outOfStockCount = Product::where('stock_quantity', '<=', 0)->count();
        $totalValue = Product::select(DB::raw('SUM(stock_quantity * price) as total'))->first()->total ?? 0;

        $categories = \App\Models\Category::all();

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Inventory', 'url' => route('inventory.index')],
        ];

        return view('pages.inventory.index', compact(
            'products',
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'totalValue',
            'categories',
            'breadcrumbs'
        ));
    }

    public function adjustForm(Product $product)
    {
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Inventory', 'url' => route('inventory.index')],
            ['title' => $product->name, 'url' => route('inventory.history', $product)],
            ['title' => 'Adjust Stock', 'url' => route('inventory.adjust.form', $product)],
        ];

        return view('pages.inventory.adjust', compact('product', 'breadcrumbs'));
    }

    public function adjust(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:adjustment,damage,expired,return',
            'reason' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:100',
        ]);

        try {
            $success = $product->updateStock(
                $validated['quantity'],
                $validated['type'],
                $validated['reference'] ?? null,
                $validated['reason'] ?? null
            );

            if (!$success) {
                return back()->with('error', 'Cannot reduce stock below zero.')->withInput();
            }

            return redirect()->route('inventory.history', $product)
                ->with('success', 'Stock adjusted successfully.');
        } catch (\Exception $e) {
            Log::error('Stock adjustment failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to adjust stock.')->withInput();
        }
    }

    public function history(Product $product)
    {
        $movements = $product->stockMovements()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Inventory', 'url' => route('inventory.index')],
            ['title' => $product->name, 'url' => route('inventory.history', $product)],
        ];

        return view('pages.inventory.history', compact('product', 'movements', 'breadcrumbs'));
    }

    public function quickAdd(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $product->updateStock(
                $request->quantity,
                'adjustment',
                null,
                'Quick add from product page'
            );

            return response()->json([
                'success' => true,
                'new_quantity' => $product->stock_quantity,
                'message' => 'Stock added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add stock.'
            ], 500);
        }
    }
}