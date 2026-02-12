<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by stock
        if ($request->filled('stock')) {
            if ($request->stock === 'low') {
                $query->lowStock();
            } elseif ($request->stock === 'out') {
                $query->outOfStock();
            }
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $products = $query->paginate(15)->withQueryString();

        $categories = Category::all();
        $totalProducts = Product::count();
        $lowStockCount = Product::lowStock()->count();
        $outOfStockCount = Product::outOfStock()->count();

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Products', 'url' => route('products.index')]
        ];

        return view('pages.products.index', compact(
            'products',
            'categories',
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'breadcrumbs'
        ));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::active()->get();
        $sku = Product::generateSku();

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Products', 'url' => route('products.index')],
            ['title' => 'Add Product', 'url' => route('products.create')]
        ];

        return view('pages.products.create', compact('categories', 'sku', 'breadcrumbs'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'nullable|in:active,inactive',
            'alcohol_percentage' => 'nullable|numeric|min:0|max:100',
            'volume_ml' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|unique:products,sku',
            'barcode' => 'nullable|string|unique:products,barcode',
            'brand' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
        ]);

        // Set default values
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['stock_quantity'] = $validated['stock_quantity'] ?? 0;
        $validated['min_stock_level'] = $validated['min_stock_level'] ?? 10;

        // Auto-generate SKU if not provided
        if (empty($validated['sku'])) {
            $validated['sku'] = Product::generateSku();
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        // Add created_by if authenticated
        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
        }

        try {
            Product::create($validated);
            return redirect()->route('products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            Log::error('Product creation failed: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to create product. Please try again.');
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load('category');

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Products', 'url' => route('products.index')],
            ['title' => $product->name, 'url' => route('products.show', $product)]
        ];

        return view('pages.products.show', compact('product', 'breadcrumbs'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->get();

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Products', 'url' => route('products.index')],
            ['title' => 'Edit Product', 'url' => route('products.edit', $product)]
        ];

        return view('pages.products.edit', compact('product', 'categories', 'breadcrumbs'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'nullable|in:active,inactive',
            'alcohol_percentage' => 'nullable|numeric|min:0|max:100',
            'volume_ml' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'brand' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
        ]);

        // Set default values if not provided
        $validated['stock_quantity'] = $validated['stock_quantity'] ?? $product->stock_quantity;
        $validated['min_stock_level'] = $validated['min_stock_level'] ?? $product->min_stock_level;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        // Add updated_by if authenticated
        if (auth()->check()) {
            $validated['updated_by'] = auth()->id();
        }

        try {
            $product->update($validated);
            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            Log::error('Product update failed: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to update product. Please try again.');
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        try {
            // Delete product image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return redirect()->route('products.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Product deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete product. It may have related sales/inventory records.');
        }
    }

    /**
     * Bulk delete products.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id'
        ]);

        try {
            $products = Product::whereIn('id', $request->ids)->get();
            
            foreach ($products as $product) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
            }

            Product::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' products deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk delete failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete products.'
            ], 500);
        }
    }

    /**
     * Update stock quantity.
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock_quantity' => 'required|integer|min:0'
        ]);

        try {
            $product->update([
                'stock_quantity' => $request->stock_quantity
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully.',
                'new_quantity' => $product->stock_quantity
            ]);
        } catch (\Exception $e) {
            Log::error('Stock update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock.'
            ], 500);
        }
    }
}