@extends('layouts.app')

@section('title', $product->name . ' - Product Details')

@section('page-title', $product->name)
@section('page-subtitle', 'Product details and information')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Product Details Card -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Product Image -->
                <div class="md:w-1/3">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                             class="w-full rounded-lg shadow-lg">
                    @else
                        <div class="w-full aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-wine-bottle text-6xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                    @endif
                </div>

                <!-- Basic Info -->
                <div class="md:w-2/3">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">{{ $product->name }}</h2>
                            @if($product->brand)
                                <p class="text-gray-600 dark:text-gray-400">{{ $product->brand }}</p>
                            @endif
                        </div>
                        <div>
                            @if($product->status == 'active')
                                <span class="px-3 py-1 text-sm font-medium bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                            @else
                                <span class="px-3 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded-full dark:bg-gray-700 dark:text-gray-300">Inactive</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">SKU</p>
                            <p class="font-medium text-gray-800 dark:text-white">{{ $product->sku ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Barcode</p>
                            <p class="font-medium text-gray-800 dark:text-white">{{ $product->barcode ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Category</p>
                            <p class="font-medium text-gray-800 dark:text-white">{{ $product->category->name ?? 'Uncategorized' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Volume</p>
                            <p class="font-medium text-gray-800 dark:text-white">{{ $product->volume_ml ? $product->volume_ml . ' ml' : 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Alcohol %</p>
                            <p class="font-medium text-gray-800 dark:text-white">{{ $product->alcohol_percentage ? $product->alcohol_percentage . '%' : 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-2">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600 dark:text-gray-400">Selling Price</span>
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($product->price, 2) }}</span>
                        </div>
                        @if($product->cost_price)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 dark:text-gray-500">Cost Price</span>
                                <span class="text-gray-700 dark:text-gray-300">{{ number_format($product->cost_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm mt-1">
                                <span class="text-gray-500 dark:text-gray-500">Profit Margin</span>
                                <span class="text-green-600 dark:text-green-400">
                                    {{ number_format(($product->price - $product->cost_price) / $product->price * 100, 1) }}%
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($product->description)
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Description</h3>
                    <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $product->description }}</p>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('products.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
                <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Are you sure you want to delete this product?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar - Stock & Stats -->
    <div class="lg:col-span-1">
        <!-- Stock Status Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Stock Status</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400">Current Stock</span>
                    <span class="text-2xl font-bold {{ $product->stock_quantity <= 0 ? 'text-red-600 dark:text-red-400' : ($product->stock_quantity <= $product->min_stock_level ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400') }}">
                        {{ $product->stock_quantity }}
                    </span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400">Min Stock Level</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $product->min_stock_level }}</span>
                </div>

                @if($product->stock_quantity <= 0)
                    <div class="p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                        <p class="text-sm text-red-700 dark:text-red-400">
                            <i class="fas fa-exclamation-circle mr-2"></i> Out of stock!
                        </p>
                    </div>
                @elseif($product->stock_quantity <= $product->min_stock_level)
                    <div class="p-3 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-sm text-yellow-700 dark:text-yellow-400">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Low stock - Reorder soon!
                        </p>
                    </div>
                @else
                    <div class="p-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
                        <p class="text-sm text-green-700 dark:text-green-400">
                            <i class="fas fa-check-circle mr-2"></i> In stock
                        </p>
                    </div>
                @endif

                <!-- Quick stock update form -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Quick Update Stock</h4>
                    <form action="{{ route('products.update-stock', $product) }}" method="POST" id="quickStockForm">
                        @csrf
                        @method('PATCH')
                        <div class="flex space-x-2">
                            <input type="number" name="stock_quantity" value="{{ $product->stock_quantity }}" min="0"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg whitespace-nowrap shadow">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('products.edit', $product) }}" class="flex items-center p-3 bg-yellow-50 dark:bg-yellow-900/30 hover:bg-yellow-100 dark:hover:bg-yellow-900/50 rounded-lg border border-yellow-200 dark:border-yellow-800 transition-colors group">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-edit text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div>
                        <span class="font-medium text-gray-800 dark:text-white">Edit Product</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Update details</p>
                    </div>
                </a>

                <a href="{{ route('products.index') }}" class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg border border-blue-200 dark:border-blue-800 transition-colors group">
                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-arrow-left text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <span class="font-medium text-gray-800 dark:text-white">Back to List</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">View all products</p>
                    </div>
                </a>

                <button onclick="window.print()" class="flex items-center p-3 w-full bg-gray-50 dark:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700 transition-colors group">
                    <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-print text-gray-600 dark:text-gray-400"></i>
                    </div>
                    <div class="text-left">
                        <span class="font-medium text-gray-800 dark:text-white">Print Details</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Generate product sheet</p>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Quick stock update via AJAX
    document.getElementById('quickStockForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const alert = document.createElement('div');
                alert.className = 'fixed top-4 right-4 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg';
                alert.innerHTML = '<div class="flex items-center"><i class="fas fa-check-circle mr-2"></i>' + data.message + '</div>';
                document.body.appendChild(alert);
                
                // Update the displayed stock count
                const stockElement = document.querySelector('.text-2xl.font-bold');
                if (stockElement) stockElement.textContent = data.new_quantity;
                
                // Remove alert after 3 seconds
                setTimeout(() => alert.remove(), 3000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating stock.');
        });
    });
</script>
@endpush
@endsection
