@extends('layouts.app')

@section('title', $category->name . ' - Category Details')

@section('page-title', $category->name)
@section('page-subtitle', 'Category details and products')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Category Details Card -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">{{ $category->name }}</h2>
                    @if($category->description)
                        <p class="text-gray-600 dark:text-gray-400">{{ $category->description }}</p>
                    @endif
                </div>
                <div>
                    @if($category->status == 'active')
                        <span class="px-3 py-1 text-sm font-medium bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                    @else
                        <span class="px-3 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded-full dark:bg-gray-700 dark:text-gray-300">Inactive</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Products</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $category->products_count ?? $category->products->count() }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Created</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $category->created_at->format('M d, Y') }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Last Updated</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $category->updated_at->format('M d, Y') }}</p>
                </div>
            </div>

            <!-- Products in this category -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Products in this Category</h3>
                
                @if($category->products->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Name</th>
                                    <th scope="col" class="px-6 py-3">SKU</th>
                                    <th scope="col" class="px-6 py-3">Price</th>
                                    <th scope="col" class="px-6 py-3">Stock</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->products as $product)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $product->name }}</td>
                                    <td class="px-6 py-4">{{ $product->sku ?? '—' }}</td>
                                    <td class="px-6 py-4">{{ number_format($product->price, 2) }}</td>
                                    <td class="px-6 py-4">
                                        @if($product->stock_quantity <= 0)
                                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full dark:bg-red-900 dark:text-red-300">Out</span>
                                        @elseif($product->stock_quantity <= $product->min_stock_level)
                                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full dark:bg-yellow-900 dark:text-yellow-300">{{ $product->stock_quantity }}</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-300">{{ $product->stock_quantity }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($product->status == 'active')
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full dark:bg-gray-700 dark:text-gray-300">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <i class="fas fa-box-open text-4xl text-gray-400 dark:text-gray-500 mb-3"></i>
                        <p class="text-gray-600 dark:text-gray-400">No products in this category yet.</p>
                        <a href="{{ route('products.create') }}?category_id={{ $category->id }}" class="inline-flex items-center mt-3 text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                            <i class="fas fa-plus mr-1"></i> Add Product
                        </a>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('categories.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
                <a href="{{ route('categories.edit', $category) }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Delete this category? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar - Quick Stats -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('categories.edit', $category) }}" class="flex items-center p-3 bg-yellow-50 dark:bg-yellow-900/30 hover:bg-yellow-100 dark:hover:bg-yellow-900/50 rounded-lg border border-yellow-200 dark:border-yellow-800 transition-colors group">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-edit text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div>
                        <span class="font-medium text-gray-800 dark:text-white">Edit Category</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Update details</p>
                    </div>
                </a>
                <a href="{{ route('products.create') }}?category_id={{ $category->id }}" class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg border border-blue-200 dark:border-blue-800 transition-colors group">
                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-plus text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <span class="font-medium text-gray-800 dark:text-white">Add Product</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">New product in this category</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
