@extends('layouts.app')

@section('title', 'Receive Stock - ' . $purchaseOrder->po_number)

@section('page-title', 'Receive Stock')
@section('page-subtitle', 'PO ' . $purchaseOrder->po_number)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Supplier</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $purchaseOrder->supplier->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Order Date</p>
                    <p class="text-base font-medium text-gray-800 dark:text-white">{{ $purchaseOrder->order_date->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                    @php
                        $statusColors = [
                            'ordered' => 'yellow',
                            'partial' => 'blue',
                        ];
                        $color = $statusColors[$purchaseOrder->status] ?? 'gray';
                    @endphp
                    <span class="px-3 py-1 text-sm font-medium bg-{{ $color }}-100 text-{{ $color }}-800 rounded-full dark:bg-{{ $color }}-900 dark:text-{{ $color }}-300 capitalize">
                        {{ $purchaseOrder->status }}
                    </span>
                </div>
            </div>
        </div>

        <form action="{{ route('purchase-orders.receive', $purchaseOrder) }}" method="POST">
            @csrf

            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Receive Items</h3>
            
            <div class="overflow-x-auto mb-6">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3">Ordered</th>
                            <th class="px-6 py-3">Received</th>
                            <th class="px-6 py-3">Remaining</th>
                            <th class="px-6 py-3">Receive Now</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->items as $item)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</td>
                                <td class="px-6 py-4">{{ $item->quantity_ordered }}</td>
                                <td class="px-6 py-4">{{ $item->quantity_received }}</td>
                                <td class="px-6 py-4">{{ $item->remaining }}</td>
                                <td class="px-6 py-4">
                                    <input type="number" name="received[{{ $item->id }}]" value="{{ $item->remaining }}" 
                                           min="0" max="{{ $item->remaining }}" 
                                           class="w-24 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="0">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-yellow-600 dark:text-yellow-400 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm text-yellow-700 dark:text-yellow-400 font-medium">Receiving Stock</p>
                        <p class="text-xs text-yellow-600 dark:text-yellow-500 mt-1">
                            Enter the quantity you are receiving now. You can receive partially and come back later to receive the remaining items.
                            When you receive stock, it will be automatically added to your inventory.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-check-circle mr-2"></i> Receive Stock
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
