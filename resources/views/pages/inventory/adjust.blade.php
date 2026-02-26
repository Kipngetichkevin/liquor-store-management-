@extends('layouts.app')

@section('title', 'Adjust Stock - ' . $product->name)

@section('page-title', 'Adjust Stock')
@section('page-subtitle', $product->name)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Current Stock</p>
                    <p class="text-3xl font-bold {{ $product->stock_quantity <= 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                        {{ $product->stock_quantity }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Min Stock Level</p>
                    <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ $product->min_stock_level }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('inventory.adjust', $product) }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- Quantity Change -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Quantity Change <span class="text-red-500">*</span>
                    </label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                            <i class="fas fa-plus-minus"></i>
                        </span>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" 
                               class="flex-1 rounded-none rounded-r-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="e.g., 10 or -5" required>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Use positive numbers to add stock, negative to remove.</p>
                    @error('quantity')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type <span class="text-red-500">*</span></label>
                    <select name="type" id="type" 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                        <option value="adjustment" {{ old('type') == 'adjustment' ? 'selected' : '' }}>Adjustment (manual)</option>
                        <option value="damage" {{ old('type') == 'damage' ? 'selected' : '' }}>Damaged</option>
                        <option value="expired" {{ old('type') == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="return" {{ old('type') == 'return' ? 'selected' : '' }}>Return</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reference (optional) -->
                <div>
                    <label for="reference" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reference</label>
                    <input type="text" name="reference" id="reference" value="{{ old('reference') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="e.g., PO-001, Invoice #">
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason</label>
                    <textarea name="reason" id="reason" rows="2" 
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Why are you adjusting stock?">{{ old('reason') }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('inventory.index') }}" class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-save mr-2"></i> Save Adjustment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
