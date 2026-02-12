@extends('layouts.app')

@section('title', 'Edit Product - Liquor Management System')

@section('page-title', 'Edit Product')
@section('page-subtitle', 'Update product information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Name -->
                <div class="col-span-2 md:col-span-1">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SKU -->
                <div class="col-span-2 md:col-span-1">
                    <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SKU</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Auto-generated if empty">
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div class="col-span-2 md:col-span-1">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" id="category_id" 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Brand -->
                <div class="col-span-2 md:col-span-1">
                    <label for="brand" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Brand</label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand', $product->brand) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="e.g., Johnnie Walker">
                    @error('brand')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div class="col-span-2 md:col-span-1">
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Selling Price <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400">KSh</span>
                        </div>
                        <input type="number" step="0.01" min="0" name="price" id="price" value="{{ old('price', $product->price) }}" 
                               class="w-full pl-12 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                    </div>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cost Price -->
                <div class="col-span-2 md:col-span-1">
                    <label for="cost_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cost Price</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400">KSh</span>
                        </div>
                        <input type="number" step="0.01" min="0" name="cost_price" id="cost_price" value="{{ old('cost_price', $product->cost_price) }}" 
                               class="w-full pl-12 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    @error('cost_price')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock Quantity -->
                <div class="col-span-2 md:col-span-1">
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stock Quantity</label>
                    <input type="number" min="0" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('stock_quantity')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Min Stock Level -->
                <div class="col-span-2 md:col-span-1">
                    <label for="min_stock_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minimum Stock Level</label>
                    <input type="number" min="0" name="min_stock_level" id="min_stock_level" value="{{ old('min_stock_level', $product->min_stock_level) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('min_stock_level')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Alcohol Percentage -->
                <div class="col-span-2 md:col-span-1">
                    <label for="alcohol_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alcohol %</label>
                    <input type="number" step="0.1" min="0" max="100" name="alcohol_percentage" id="alcohol_percentage" value="{{ old('alcohol_percentage', $product->alcohol_percentage) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="e.g., 40.0">
                    @error('alcohol_percentage')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Volume (ml) -->
                <div class="col-span-2 md:col-span-1">
                    <label for="volume_ml" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Volume (ml)</label>
                    <input type="number" min="0" name="volume_ml" id="volume_ml" value="{{ old('volume_ml', $product->volume_ml) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="e.g., 750">
                    @error('volume_ml')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Barcode -->
                <div class="col-span-2 md:col-span-1">
                    <label for="barcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Barcode</label>
                    <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $product->barcode) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('barcode')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="col-span-2 md:col-span-1">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" id="status" 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Image -->
                <div class="col-span-2">
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product Image</label>
                    
                    @if($product->image)
                        <div class="mb-4 flex items-center">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover rounded-lg shadow mr-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Current image</p>
                                <label class="inline-flex items-center mt-2">
                                    <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Remove image</span>
                                </label>
                            </div>
                        </div>
                    @endif
                    
                    <input type="file" name="image" id="image" accept="image/*"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-600 dark:file:text-gray-300">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4" 
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Product description...">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('products.index') }}" class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-save mr-2"></i> Update Product
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
