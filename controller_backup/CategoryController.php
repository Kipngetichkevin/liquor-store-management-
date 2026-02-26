<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Eager load products count
        $categories = $query->withCount('products')->paginate(15);

        // Stats for the view
        $totalCategories = Category::count();
        $activeCount = Category::where('status', 'active')->count();
        $inactiveCount = Category::where('status', 'inactive')->count();

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Categories', 'url' => route('categories.index')]
        ];

        return view('pages.categories.index', compact(
            'categories',
            'totalCategories',
            'activeCount',
            'inactiveCount',
            'breadcrumbs'
        ));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Categories', 'url' => route('categories.index')],
            ['title' => 'Add Category', 'url' => route('categories.create')]
        ];

        return view('pages.categories.create', compact('breadcrumbs'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        // Set default values
        $validated['status'] = $validated['status'] ?? 'active';
        
        // Generate slug (model boot will also do this, but we set it explicitly)
        $validated['slug'] = Str::slug($validated['name']);

        // Add created_by if authenticated
        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
        }

        try {
            Category::create($validated);

            return redirect()->route('categories.index')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            Log::error('Category creation failed: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to create category. Please try again.');
        }
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        $category->load('products');

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Categories', 'url' => route('categories.index')],
            ['title' => $category->name, 'url' => route('categories.show', $category)]
        ];

        return view('pages.categories.show', compact('category', 'breadcrumbs'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Categories', 'url' => route('categories.index')],
            ['title' => 'Edit Category', 'url' => route('categories.edit', $category)]
        ];

        return view('pages.categories.edit', compact('category', 'breadcrumbs'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        // Update slug if name changed (model boot will also do this)
        if ($category->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Add updated_by if authenticated
        if (auth()->check()) {
            $validated['updated_by'] = auth()->id();
        }

        try {
            $category->update($validated);

            return redirect()->route('categories.index')
                ->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            Log::error('Category update failed: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to update category. Please try again.');
        }
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        try {
            // Check if category has products
            if ($category->products()->count() > 0) {
                return back()->with('error', 'Cannot delete category because it has associated products.');
            }

            $category->delete();

            return redirect()->route('categories.index')
                ->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Category deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete category. Please try again.');
        }
    }
}
