@extends('layouts.app')

@section('title', 'PO ' . $purchaseOrder->po_number)

@section('page-title', 'Purchase Order ' . $purchaseOrder->po_number)
@section('page-subtitle', 'Order details and status')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Order Details -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <!-- Header with Status -->
            <div class="flex flex-col md:flex-row justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Purchase Order {{ $purchaseOrder->po_number }}</h2>
                    <p class="text-gray-600 dark:text-gray-400">Created by {{ $purchaseOrder->user->name ?? 'System' }} on {{ $purchaseOrder->created_at->format('d M Y H:i') }}</p>
                </div>
                <div class="mt-4 md:mt-0">
                    @php
                        $statusColors = [
                            'draft' => 'gray',
                            'ordered' => 'yellow',
                            'partial' => 'blue',
                            'received' => 'green',
                            'cancelled' => 'red',
                        ];
                        $color = $statusColors[$purchaseOrder->status] ?? 'gray';
                    @endphp
                    <span class="px-4 py-2 text-sm font-medium bg-{{ $color }}-100 text-{{ $color }}-800 rounded-full dark:bg-{{ $color }}-900 dark:text-{{ $color }}-300 capitalize">
                        {{ $purchaseOrder->status }}
                    </span>
                </div>
            </div>

            <!-- Supplier Info -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Supplier Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Supplier</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $purchaseOrder->supplier->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Contact</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $purchaseOrder->supplier->contact_person ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $purchaseOrder->supplier->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $purchaseOrder->supplier->phone ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Dates -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Order Date</p>
                    <p class="font-medium text-gray-800 dark:text-white">{{ $purchaseOrder->order_date->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Expected Date</p>
                    <p class="font-medium text-gray-800 dark:text-white">{{ $purchaseOrder->expected_date?->format('d M Y') ?? 'Not set' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Received Date</p>
                    <p class="font-medium text-gray-800 dark:text-white">{{ $purchaseOrder->received_date?->format('d M Y') ?? 'Not received' }}</p>
                </div>
            </div>

            <!-- Order Items -->
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Order Items</h3>
            <div class="overflow-x-auto mb-6">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3">Ordered</th>
                            <th class="px-6 py-3">Received</th>
                            <th class="px-6 py-3">Unit Cost</th>
                            <th class="px-6 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->items as $item)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</td>
                                <td class="px-6 py-4">{{ $item->quantity_ordered }}</td>
                                <td class="px-6 py-4">
                                    <span class="{{ $item->quantity_received >= $item->quantity_ordered ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                        {{ $item->quantity_received }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ number_format($item->unit_cost, 2) }}</td>
                                <td class="px-6 py-4">{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="font-semibold text-gray-900 dark:text-white">
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-right">Subtotal:</td>
                            <td class="px-6 py-3">{{ number_format($purchaseOrder->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-right">Total:</td>
                            <td class="px-6 py-3">{{ number_format($purchaseOrder->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Notes -->
            @if($purchaseOrder->notes)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</h4>
                    <p class="text-gray-600 dark:text-gray-400">{{ $purchaseOrder->notes }}</p>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('purchase-orders.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
                
                @if($purchaseOrder->status == 'draft')
                    <form action="{{ route('purchase-orders.mark-ordered', $purchaseOrder) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                            <i class="fas fa-check mr-2"></i> Mark as Ordered
                        </button>
                    </form>
                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition shadow">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                @endif

                @if(in_array($purchaseOrder->status, ['ordered', 'partial']))
                    <a href="{{ route('purchase-orders.receive-form', $purchaseOrder) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition shadow">
                        <i class="fas fa-truck-loading mr-2"></i> Receive Stock
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar - Quick Actions -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Order Summary</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Items:</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $purchaseOrder->items->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Total Quantity:</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $purchaseOrder->items->sum('quantity_ordered') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Received:</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $purchaseOrder->items->sum('quantity_received') }}</span>
                </div>
                <div class="flex justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">Order Total:</span>
                    <span class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($purchaseOrder->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
