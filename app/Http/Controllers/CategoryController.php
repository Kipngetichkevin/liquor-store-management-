<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Traits\RoleCheckTrait;

class CategoryController extends Controller
{
    use RoleCheckTrait;

    public function index(Request $request)
    {
        $check = $this->checkRole(['admin', 'manager', 'stock_keeper']);
        if ($check !== true) return $check;

        $query = Category::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $categories = $query->withCount('products')->paginate(15);

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

    public function create()
    {
        $check = $this->checkRole(['admin', 'manager', 'stock_keeper']);
        if ($check !== true) return $check;

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Categories', 'url' => route('categories.index')],
            ['title' => 'Add Category', 'url' => route('categories.create')]
        ];

        return view('pages.categories.create', compact('breadcrumbs'));
    }

    public function store(Request $request)
    {
        $check = $this->checkRole(['admin', 'manager', 'stock_keeper']);
        if ($check !== true) return $check;

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['status'] = $validated['status'] ?? 'active';
        $validated['slug'] = Str::slug($validated['name']);

        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
        }

        try {
            $category = Category::create($validated);
            auth()->user()->logActivity('create', 'categories', 'Created category: ' . $category->name);
            return redirect()->route('categories.index')->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            Log::error('Category creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create category.');
        }
    }

    public function show(Category $category)
    {
        $check = $this->checkRole(['admin', 'manager', 'stock_keeper']);
        if ($check !== true) return $check;

        $category->load('products');
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Categories', 'url' => route('categories.index')],
            ['title' => $category->name, 'url' => route('categories.show', $category)]
        ];
        return view('pages.categories.show', compact('category', 'breadcrumbs'));
    }

    public function edit(Category $category)
    {
        $check = $this->checkRole(['admin', 'manager', 'stock_keeper']);
        if ($check !== true) return $check;

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Categories', 'url' => route('categories.index')],
            ['title' => 'Edit Category', 'url' => route('categories.edit', $category)]
        ];
        return view('pages.categories.edit', compact('category', 'breadcrumbs'));
    }

    public function update(Request $request, Category $category)
    {
        $check = $this->checkRole(['admin', 'manager', 'stock_keeper']);
        if ($check !== true) return $check;

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($category->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if (auth()->check()) {
            $validated['updated_by'] = auth()->id();
        }

        try {
            $category->update($validated);
            auth()->user()->logActivity('update', 'categories', 'Updated category: ' . $category->name);
            return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            Log::error('Category update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update category.');
        }
    }

    public function destroy(Category $category)
    {
        $check = $this->checkRole(['admin']);
        if ($check !== true) return $check;

        try {
            if ($category->products()->count() > 0) {
                return back()->with('error', 'Cannot delete category because it has associated products.');
            }
            $name = $category->name;
            $category->delete();
            auth()->user()->logActivity('delete', 'categories', 'Deleted category: ' . $name);
            return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Category deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete category.');
        }
    }
}