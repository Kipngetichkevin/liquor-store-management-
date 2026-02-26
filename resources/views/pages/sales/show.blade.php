@extends('layouts.app')

@section('title', 'Sale Details - ' . $sale->invoice_number)

@section('page-title', 'Sale Details')
@section('page-subtitle', $sale->invoice_number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Sale Details -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <!-- Header with Status -->
            <div class="flex flex-col md:flex-row justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Sale {{ $sale->invoice_number }}</h2>
                    <p class="text-gray-600 dark:text-gray-400">Processed by {{ $sale->user->name ?? 'System' }} on {{ $sale->created_at->format('d M Y H:i') }}</p>
                </div>
                <div class="mt-4 md:mt-0">
                    @php
                        $statusColors = [
                            'completed' => 'green',
                            'pending' => 'yellow',
                            'cancelled' => 'red',
                        ];
                        $color = $statusColors[$sale->status] ?? 'gray';
                    @endphp
                    <span class="px-4 py-2 text-sm font-medium bg-{{ $color }}-100 text-{{ $color }}-800 rounded-full dark:bg-{{ $color }}-900 dark:text-{{ $color }}-300 capitalize">
                        {{ $sale->status }}
                    </span>
                </div>
            </div>

            <!-- Sale Items -->
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Items Sold</h3>
            <div class="overflow-x-auto mb-6">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3">Quantity</th>
                            <th class="px-6 py-3">Unit Price</th>
                            <th class="px-6 py-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $item->product->name ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-4">{{ $item->quantity }}</td>
                                <td class="px-6 py-4">KSh {{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-4">KSh {{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Payment Summary -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Payment Summary</h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                        <span class="font-medium text-gray-800 dark:text-white">KSh {{ number_format($sale->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Tax (16%):</span>
                        <span class="font-medium text-gray-800 dark:text-white">KSh {{ number_format($sale->tax_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Discount:</span>
                        <span class="font-medium text-gray-800 dark:text-white">KSh {{ number_format($sale->discount_amount, 2) }}</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-600 my-2"></div>
                    <div class="flex justify-between text-lg font-bold">
                        <span class="text-gray-800 dark:text-white">Total:</span>
                        <span class="text-blue-600 dark:text-blue-400">KSh {{ number_format($sale->total_amount, 2) }}</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-600 my-2"></div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Payment Method:</span>
                        <span class="font-medium capitalize text-gray-800 dark:text-white">{{ $sale->payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Amount Paid:</span>
                        <span class="font-medium text-gray-800 dark:text-white">KSh {{ number_format($sale->amount_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Change:</span>
                        <span class="font-medium text-gray-800 dark:text-white">KSh {{ number_format($sale->change, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($sale->notes)
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</h4>
                    <p class="text-gray-600 dark:text-gray-400">{{ $sale->notes }}</p>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('sales.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
                <a href="{{ route('sales.print', $sale) }}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-print mr-2"></i> Print Receipt
                </a>
                @if($sale->status !== 'cancelled')
                    <form action="{{ route('sales.void', $sale) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to void this sale? Stock will be restored.')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow">
                            <i class="fas fa-ban mr-2"></i> Void Sale
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar - Quick Info -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Sale Summary</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Invoice:</span>
                    <span class="font-mono text-sm font-medium text-gray-800 dark:text-white">{{ $sale->invoice_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Date:</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $sale->created_at->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Time:</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $sale->created_at->format('H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Items:</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $sale->items->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Total Quantity:</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $sale->items->sum('quantity') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
