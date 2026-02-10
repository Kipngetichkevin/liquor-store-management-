<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }
    
    public function create()
    {
        return view('categories.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);
        
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);
        
        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully!');
    }
    
    public function show($id)
    {
        $category = Category::findOrFail($id);
        
        if (method_exists($category, 'products')) {
            $category->load('products');
        } else {
            $category->setRelation('products', collect());
        }
        
        return view('categories.show', compact('category'));
    }
    
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('categories.edit', compact('category'));
    }
    
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);
        
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
        ]);
        
        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully!');
    }
    
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}