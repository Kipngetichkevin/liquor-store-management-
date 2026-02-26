@extends('layouts.app')

@section('title', 'Stock History - ' . $product->name)

@section('page-title', $product->name)
@section('page-subtitle', 'Stock movement history')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Product Summary Card -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="text-center mb-4">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg mx-auto mb-4 shadow">
                @else
                    <div class="w-32 h-32 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-wine-bottle text-4xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                @endif
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $product->name }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->sku ?? 'No SKU' }}</p>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600 dark:text-gray-400">Current Stock</span>
                    <span class="text-2xl font-bold {{ $product->stock_quantity <= 0 ? 'text-red-600 dark:text-red-400' : ($product->stock_quantity <= $product->min_stock_level ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400') }}">
                        {{ $product->stock_quantity }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400">Min Stock Level</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $product->min_stock_level }}</span>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('inventory.adjust.form', $product) }}" class="w-full flex items-center justify-center p-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-balance-scale mr-2"></i> Adjust Stock
                </a>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="lg:col-span-3">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Stock Movements</h3>

            <div class="overflow-x-auto">
                @if($movements->count() > 0)
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Change</th>
                                <th class="px-6 py-3">New Stock</th>
                                <th class="px-6 py-3">Reference</th>
                                <th class="px-6 py-3">Reason</th>
                                <th class="px-6 py-3">User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movements as $movement)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4">{{ $movement->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $typeColors = [
                                                'purchase' => 'green',
                                                'sale' => 'blue',
                                                'adjustment' => 'yellow',
                                                'return' => 'purple',
                                                'damage' => 'red',
                                                'expired' => 'gray',
                                            ];
                                            $color = $typeColors[$movement->type] ?? 'gray';
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800 rounded-full dark:bg-{{ $color }}-900 dark:text-{{ $color }}-300 capitalize">
                                            {{ $movement->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 {{ $movement->quantity_change > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $movement->quantity_change > 0 ? '+' . $movement->quantity_change : $movement->quantity_change }}
                                    </td>
                                    <td class="px-6 py-4">{{ $movement->new_quantity }}</td>
                                    <td class="px-6 py-4">{{ $movement->reference ?? '—' }}</td>
                                    <td class="px-6 py-4 max-w-xs truncate">{{ $movement->reason ?? '—' }}</td>
                                    <td class="px-6 py-4">{{ $movement->user->name ?? 'System' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-6">{{ $movements->links() }}</div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-history text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">No stock movements recorded yet.</p>
                        <a href="{{ route('inventory.adjust.form', $product) }}" class="inline-flex items-center mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                            <i class="fas fa-plus mr-2"></i> Add Stock Movement
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
