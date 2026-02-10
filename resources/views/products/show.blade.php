<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - Liquor Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold">{{ $product->name }}</h1>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="text-gray-600">SKU: {{ $product->sku }}</span>
                    <span class="text-gray-400">•</span>
                    <a href="{{ route('categories.show', $product->category_id) }}" class="text-blue-600 hover:text-blue-800">
                        {{ $product->category->name ?? 'Uncategorized' }}
                    </a>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('products.edit', $product->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Edit Product
                </a>
                <a href="{{ route('products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Back to Products
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column - Product Image & Basic Info -->
            <div class="md:col-span-1">
                <div class="bg-white shadow rounded-lg p-6">
                    <!-- Product Image -->
                    <div class="mb-6">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}"
                                 class="w-full h-64 object-cover rounded-lg">
                        @else
                            <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Product Status -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <div class="mt-1 flex items-center">
                                @if($product->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                                <span class="ml-2 text-sm text-gray-600">
                                    @if($product->is_active)
                                        Available for sale
                                    @else
                                        Not available for sale
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Stock Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Stock Status</label>
                            <div class="mt-1">
                                @if($product->quantity <= 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Out of Stock
                                    </span>
                                @elseif($product->quantity <= $product->min_stock_level)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Low Stock ({{ $product->quantity }} left)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        In Stock ({{ $product->quantity }} available)
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Liquor Details -->
                        @if($product->bottle_size || $product->alcohol_percentage || $product->brand)
                        <div class="pt-4 border-t border-gray-200">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Product Details</h3>
                            <div class="space-y-2">
                                @if($product->bottle_size)
                                    <div>
                                        <span class="text-sm text-gray-600">Size:</span>
                                        <span class="text-sm font-medium ml-1">{{ $product->bottle_size }}</span>
                                    </div>
                                @endif
                                @if($product->alcohol_percentage)
                                    <div>
                                        <span class="text-sm text-gray-600">Alcohol:</span>
                                        <span class="text-sm font-medium ml-1">{{ $product->alcohol_percentage }}%</span>
                                    </div>
                                @endif
                                @if($product->brand)
                                    <div>
                                        <span class="text-sm text-gray-600">Brand:</span>
                                        <span class="text-sm font-medium ml-1">{{ $product->brand }}</span>
                                    </div>
                                @endif
                                @if($product->country_of_origin)
                                    <div>
                                        <span class="text-sm text-gray-600">Origin:</span>
                                        <span class="text-sm font-medium ml-1">{{ $product->country_of_origin }}</span>
                                    </div>
                                @endif
                                @if($product->expiry_date)
                                    <div>
                                        <span class="text-sm text-gray-600">Expiry:</span>
                                        <span class="text-sm font-medium ml-1">{{ \Carbon\Carbon::parse($product->expiry_date)->format('M d, Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Timestamps -->
                        <div class="pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Created</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $product->updated_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Product Details -->
            <div class="md:col-span-2">
                <!-- Pricing Card -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Pricing Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500">Selling Price</label>
                                <p class="mt-1 text-2xl font-bold text-blue-600">${{ number_format($product->price, 2) }}</p>
                            </div>
                            
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500">Cost Price</label>
                                <p class="mt-1 text-2xl font-bold text-green-600">${{ number_format($product->cost_price, 2) }}</p>
                            </div>
                            
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500">Profit</label>
                                <p class="mt-1 text-2xl font-bold text-purple-600">
                                    ${{ number_format($product->price - $product->cost_price, 2) }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ round((($product->price - $product->cost_price) / $product->price) * 100, 2) }}% margin
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Information Card -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Stock Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Stock</label>
                                <div class="text-3xl font-bold text-gray-900">{{ $product->quantity }}</div>
                                <p class="text-sm text-gray-500 mt-1">Units in inventory</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Stock Level</label>
                                <div class="text-3xl font-bold text-gray-900">{{ $product->min_stock_level }}</div>
                                <p class="text-sm text-gray-500 mt-1">Low stock alert threshold</p>
                            </div>
                        </div>
                        
                        <!-- Stock Progress Bar -->
                        <div class="mt-6">
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">Stock Level</span>
                                <span class="text-sm font-medium text-gray-700">
                                    {{ $product->quantity }} of {{ max($product->quantity, $product->min_stock_level * 3) }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                @php
                                    $maxStock = max($product->quantity, $product->min_stock_level * 3);
                                    $percentage = min(100, ($product->quantity / $maxStock) * 100);
                                    $color = $product->quantity <= 0 ? 'bg-red-600' : 
                                             ($product->quantity <= $product->min_stock_level ? 'bg-yellow-400' : 'bg-green-600');
                                @endphp
                                <div class="{{ $color }} h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1 text-xs text-gray-500">
                                <span>Empty</span>
                                <span>Low Stock ({{ $product->min_stock_level }})</span>
                                <span>Healthy</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Card -->
                @if($product->description)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Product Description</h2>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 whitespace-pre-line">{{ $product->description }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>