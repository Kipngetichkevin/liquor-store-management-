<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} - Liquor Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold">{{ $category->name }}</h1>
                <p class="text-gray-600">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <span class="ml-2 text-gray-500">Category Details</span>
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('categories.edit', $category->id) }}" 
                   class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Edit Category
                </a>
                <a href="{{ route('categories.index') }}" 
                   class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Back to Categories
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Category Information -->
            <div class="md:col-span-1">
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Category Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Name</label>
                            <p class="mt-1 text-gray-900">{{ $category->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Slug</label>
                            <p class="mt-1 text-gray-900">{{ $category->slug }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Description</label>
                            <p class="mt-1 text-gray-900">{{ $category->description ?: 'No description provided' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <p class="mt-1">
                                @if($category->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                    <span class="text-gray-500 text-sm ml-2">(Visible in product selection)</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                    <span class="text-gray-500 text-sm ml-2">(Hidden from product selection)</span>
                                @endif
                            </p>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Created</label>
                                    <p class="mt-1 text-gray-900">{{ $category->created_at->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                                    <p class="mt-1 text-gray-900">{{ $category->updated_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products in this Category -->
            <div class="md:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Products in this Category</h2>
                    </div>
                    <div class="p-6">
                        @if($category->products && $category->products->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($category->products as $product)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center">
                                                        @if($product->image)
                                                            <img src="{{ asset('storage/' . $product->image) }}" 
                                                                 alt="{{ $product->name }}"
                                                                 class="h-10 w-10 rounded-full object-cover mr-3">
                                                        @endif
                                                        <div>
                                                            <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                                            <div class="text-sm text-gray-500">{{ $product->sku }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-gray-900">${{ number_format($product->price, 2) }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center">
                                                        <span class="mr-2">{{ $product->stock_quantity }}</span>
                                                        @if($product->stock_quantity <= 0)
                                                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Out</span>
                                                        @elseif($product->stock_quantity <= $product->min_stock)
                                                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Low</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if($product->is_active)
                                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Active</span>
                                                    @else
                                                        <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="mt-2 text-gray-500">No products found in this category.</p>
                                <p class="text-sm text-gray-400 mt-1">Products will appear here when you add them.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>