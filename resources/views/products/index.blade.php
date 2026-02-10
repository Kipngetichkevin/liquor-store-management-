<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Liquor Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold">Products</h1>
                <p class="text-gray-600">Manage your inventory</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Categories
                </a>
                <a href="{{ route('products.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Add Product
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Category</th>
                        <th class="px-6 py-3 text-left">Price</th>
                        <th class="px-6 py-3 text-left">Stock</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $product->name }}</td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                {{ $product->category->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium">${{ number_format($product->price, 2) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <span class="mr-2">{{ $product->quantity }}</span>
                                @if($product->quantity <= $product->min_stock_level)
                                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Low</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($product->is_active)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Active</span>
                            @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('products.show', $product->id) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                <a href="{{ route('products.edit', $product->id) }}" class="text-green-600 hover:text-green-800">Edit</a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-800"
                                            onclick="return confirm('Delete this product?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <p class="mt-2">No products found.</p>
                            <a href="{{ route('products.create') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                                Add your first product
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>