@extends('layouts.app')

@section('title', 'Product Sales Report')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Product Sales Report</h1>
        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mt-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Dashboard</a>
            <span class="mx-2">/</span>
            <span>Product Sales</span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
            <i class="fas fa-filter mr-2 text-blue-600"></i> Filter by Date Range
        </h3>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="start" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From</label>
                <input type="date" name="start" id="start" value="{{ $start }}"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="end" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To</label>
                <input type="date" name="end" id="end" value="{{ $end }}"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="md:col-span-2 flex items-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-search mr-2"></i> Apply Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
            <i class="fas fa-table mr-2 text-green-600"></i> Products
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">SKU</th>
                        <th class="px-6 py-3">Quantity Sold</th>
                        <th class="px-6 py-3">Revenue (KSh)</th>
                        <th class="px-6 py-3">Avg Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $product->name }}</td>
                        <td class="px-6 py-4">{{ $product->sku }}</td>
                        <td class="px-6 py-4">{{ $product->total_quantity }}</td>
                        <td class="px-6 py-4">KSh {{ number_format($product->total_revenue, 2) }}</td>
                        <td class="px-6 py-4">KSh {{ number_format($product->avg_profit, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            No sales data found for this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection