<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Liquor Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Add New Product</h1>
            <a href="{{ route('products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to Products
            </a>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div>
                        <div class="mb-4">
                            <label for="category_id" class="block text-gray-700 font-medium mb-2">Category *</label>
                            <select id="category_id" 
                                    name="category_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 font-medium mb-2">Product Name *</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., Jack Daniel's Old No. 7"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label for="sku" class="block text-gray-700 font-medium mb-2">SKU</label>
                            <input type="text" 
                                   id="sku" 
                                   name="sku" 
                                   value="{{ old('sku') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Auto-generated if left empty">
                            <p class="text-gray-500 text-sm mt-1">Stock Keeping Unit (leave blank to auto-generate)</p>
                        </div>

                        <div class="mb-4">
                            <label for="barcode" class="block text-gray-700 font-medium mb-2">Barcode</label>
                            <input type="text" 
                                   id="barcode" 
                                   name="barcode" 
                                   value="{{ old('barcode') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Optional barcode">
                        </div>

                        <div class="mb-4">
                            <label for="image" class="block text-gray-700 font-medium mb-2">Product Image</label>
                            <input type="file" 
                                   id="image" 
                                   name="image"
                                   accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-500 text-sm mt-1">JPG, PNG, GIF (Max 2MB)</p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>
                        <div class="mb-4">
                            <label for="price" class="block text-gray-700 font-medium mb-2">Selling Price *</label>
                            <input type="number" 
                                   id="price" 
                                   name="price" 
                                   value="{{ old('price') }}"
                                   step="0.01"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="0.00"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label for="cost_price" class="block text-gray-700 font-medium mb-2">Cost Price *</label>
                            <input type="number" 
                                   id="cost_price" 
                                   name="cost_price" 
                                   value="{{ old('cost_price') }}"
                                   step="0.01"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="0.00"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label for="quantity" class="block text-gray-700 font-medium mb-2">Current Stock *</label>
                            <input type="number" 
                                   id="quantity" 
                                   name="quantity" 
                                   value="{{ old('quantity', 0) }}"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label for="min_stock_level" class="block text-gray-700 font-medium mb-2">Minimum Stock Level *</label>
                            <input type="number" 
                                   id="min_stock_level" 
                                   name="min_stock_level" 
                                   value="{{ old('min_stock_level', 10) }}"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Alert when stock is below this"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Product description">{{ old('description') }}</textarea>
                        </div>

                        <!-- Liquor Specific Fields -->
                        <div class="mb-4">
                            <label for="bottle_size" class="block text-gray-700 font-medium mb-2">Bottle Size</label>
                            <input type="text" 
                                   id="bottle_size" 
                                   name="bottle_size" 
                                   value="{{ old('bottle_size') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., 750ml, 1L">
                        </div>

                        <div class="mb-4">
                            <label for="alcohol_percentage" class="block text-gray-700 font-medium mb-2">Alcohol %</label>
                            <input type="number" 
                                   id="alcohol_percentage" 
                                   name="alcohol_percentage" 
                                   value="{{ old('alcohol_percentage') }}"
                                   step="0.1"
                                   min="0"
                                   max="100"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., 40.0">
                        </div>

                        <div class="mb-4">
                            <label for="brand" class="block text-gray-700 font-medium mb-2">Brand</label>
                            <input type="text" 
                                   id="brand" 
                                   name="brand" 
                                   value="{{ old('brand') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., Jack Daniel's">
                        </div>

                        <div class="mb-4">
                            <label for="country_of_origin" class="block text-gray-700 font-medium mb-2">Country of Origin</label>
                            <input type="text" 
                                   id="country_of_origin" 
                                   name="country_of_origin" 
                                   value="{{ old('country_of_origin') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., USA, Scotland">
                        </div>

                        <div class="mb-4">
                            <label for="expiry_date" class="block text-gray-700 font-medium mb-2">Expiry Date</label>
                            <input type="date" 
                                   id="expiry_date" 
                                   name="expiry_date" 
                                   value="{{ old('expiry_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-gray-700">Product is Active</span>
                            </label>
                            <p class="text-gray-500 text-sm mt-1">Inactive products won't appear in sales.</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" 
                            class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>