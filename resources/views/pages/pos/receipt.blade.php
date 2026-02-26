@extends('layouts.app')

@section('title', 'Receipt - ' . $sale->invoice_number)

@section('page-title', 'Sale Receipt')
@section('page-subtitle', $sale->invoice_number)

@push('styles')
<style>
    /* Print styles – hide everything except the receipt */
    @media print {
        /* Hide everything outside the receipt container */
        body * {
            visibility: hidden;
        }
        /* Show only the receipt container and its children */
        .receipt-container, .receipt-container * {
            visibility: visible;
        }
        /* Position the receipt at the top of the printed page */
        .receipt-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 1rem;
            background: white;
            color: black;
        }
        /* Remove extra page margins */
        @page {
            margin: 1cm;
        }
        /* Style adjustments for better print appearance */
        .receipt-container .bg-white {
            background: white !important;
            border: none !important;
            box-shadow: none !important;
        }
        .receipt-container .text-gray-800 {
            color: black !important;
        }
        .receipt-container .dark\:bg-gray-800 {
            background: white !important;
        }
        /* Hide print button and New Sale button */
        .receipt-container .no-print {
            display: none;
        }
        /* Ensure tables look clean */
        .receipt-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .receipt-container th {
            background: #f3f4f6 !important;
            color: black !important;
            font-weight: 600;
        }
        .receipt-container td, .receipt-container th {
            padding: 0.5rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
    }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="receipt-container">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Liquor Store</h2>
                <p class="text-gray-600 dark:text-gray-400">Point of Sale Receipt</p>
            </div>

            <div class="border-t border-b border-gray-200 dark:border-gray-700 py-4 mb-4">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600 dark:text-gray-400">Invoice No:</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $sale->invoice_number }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600 dark:text-gray-400">Date:</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $sale->created_at->format('d M Y H:i') }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600 dark:text-gray-400">Cashier:</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $sale->user->name ?? 'System' }}</span>
                </div>
            </div>

            <table class="w-full mb-4">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-2 text-left">Item</th>
                        <th class="px-4 py-2 text-center">Qty</th>
                        <th class="px-4 py-2 text-right">Price (inc. VAT)</th>
                        <th class="px-4 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    <tr class="border-b dark:border-gray-700">
                        <td class="px-4 py-2">{{ $item->product->name }}</td>
                        <td class="px-4 py-2 text-center">{{ $item->quantity }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($item->total_price / $item->quantity, 2) }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal (excl. tax):</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ number_format($sale->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600 dark:text-gray-400">Tax ({{ config('pos.tax_rate', 16) }}%):</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ number_format($sale->tax_amount, 2) }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600 dark:text-gray-400">Discount:</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ number_format($sale->discount_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold mb-4">
                    <span class="text-gray-800 dark:text-white">Total:</span>
                    <span class="text-blue-600 dark:text-blue-400">{{ number_format($sale->total_amount, 2) }}</span>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600 dark:text-gray-400">Payment Method:</span>
                        <span class="font-medium text-gray-800 dark:text-white capitalize">{{ $sale->payment_method }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600 dark:text-gray-400">Amount Paid:</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ number_format($sale->amount_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600 dark:text-gray-400">Change:</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ number_format($sale->change, 2) }}</span>
                    </div>
                </div>

                @if($sale->notes)
                <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Notes: {{ $sale->notes }}</p>
                </div>
                @endif
            </div>

            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 no-print">
                <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-plus mr-2"></i> New Sale
                </a>
                <button onclick="window.print()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection