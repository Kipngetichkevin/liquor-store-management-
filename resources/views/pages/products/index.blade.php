@extends('layouts.app')

@section('title', 'Products - Liquor Management System')

@section('page-title', 'Products')
@section('page-subtitle', 'Manage your liquor inventory')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Sidebar Filters -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Filters</h3>
            
            <form method="GET" action="{{ route('products.index') }}" id="filterForm">
                <!-- Search -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Name, SKU, brand...">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                    <select name="category" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stock Level</label>
                    <select name="stock" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All</option>
                        <option value="low" {{ request('stock') == 'low' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out" {{ request('stock') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sort By</label>
                    <select name="sort" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>Date Added</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Price</option>
                        <option value="stock_quantity" {{ request('sort') == 'stock_quantity' ? 'selected' : '' }}>Stock</option>
                    </select>
                    <select name="direction" class="w-full mt-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="desc" {{ request('direction', 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition shadow">Apply Filters</button>
                    <a href="{{ route('products.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg text-center transition shadow">Reset</a>
                </div>
            </form>
        </div>
    </div>
    <div class="lg:col-span-3">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div><p class="text-sm text-gray-500 dark:text-gray-400">Total Products</p><p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalProducts ?? 0 }}</p></div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg"><i class="fas fa-wine-bottle text-blue-600 dark:text-blue-400"></i></div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div><p class="text-sm text-gray-500 dark:text-gray-400">Low Stock</p><p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $lowStockCount ?? 0 }}</p></div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg"><i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400"></i></div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div><p class="text-sm text-gray-500 dark:text-gray-400">Out of Stock</p><p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $outOfStockCount ?? 0 }}</p></div>
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg"><i class="fas fa-times-circle text-red-600 dark:text-red-400"></i></div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 sm:mb-0">Product List</h3>
                <a href="{{ route('products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center shadow"><i class="fas fa-plus mr-2"></i> Add Product</a>
            </div>
            <div class="overflow-x-auto">
                @if(isset($products) && $products->count() > 0)
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr><th scope="col" class="px-6 py-3">Image</th><th scope="col" class="px-6 py-3">Name/SKU</th><th scope="col" class="px-6 py-3">Category</th><th scope="col" class="px-6 py-3">Price</th><th scope="col" class="px-6 py-3">Stock</th><th scope="col" class="px-6 py-3">Status</th><th scope="col" class="px-6 py-3">Actions</th></tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4">
                                    @if($product->image)<img src="{{ asset('storage/'.$product->image) }}" alt="" class="w-12 h-12 object-cover rounded-lg shadow-sm">
                                    @else<div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center shadow-sm"><i class="fas fa-wine-bottle text-gray-400 dark:text-gray-500"></i></div>@endif
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><div>{{ $product->name }}</div><div class="text-xs text-gray-500 dark:text-gray-400">{{ $product->sku ?? 'No SKU' }}</div></td>
                                <td class="px-6 py-4">{{ optional($product->category)->name ?? 'Uncategorized' }}</td>
                                <td class="px-6 py-4"><div class="font-medium">{{ number_format($product->price, 2) }}</div>@if($product->cost_price)<div class="text-xs text-gray-500">Cost: {{ number_format($product->cost_price, 2) }}</div>@endif</td>
                                <td class="px-6 py-4">
                                    @if($product->stock_quantity <= 0)<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full dark:bg-red-900 dark:text-red-300">Out</span>
                                    @elseif($product->stock_quantity <= $product->min_stock_level)<span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full dark:bg-yellow-900 dark:text-yellow-300">{{ $product->stock_quantity }}</span>
                                    @else<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-300">{{ $product->stock_quantity }}</span>@endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($product->status == 'active')<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                                    @else<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full dark:bg-gray-700 dark:text-gray-300">Inactive</span>@endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('products.edit', $product) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Are you sure you want to delete this product?')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete"><i class="fas fa-trash"></i></button></form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-6">{{ $products->links() }}</div>
                @else
                    <div class="text-center py-12"><div class="mb-4"><i class="fas fa-wine-bottle text-6xl text-gray-300 dark:text-gray-600"></i></div><h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No products found</h4><p class="text-gray-500 dark:text-gray-400 mb-4">Get started by adding your first product.</p><a href="{{ route('products.create') }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow"><i class="fas fa-plus mr-2"></i> Add Product</a></div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
