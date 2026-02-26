@extends('layouts.app')

@section('title', 'Daily Sales Report')

@section('page-title', 'Daily Sales Report')
@section('page-subtitle', 'Sales summary for a single day')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Date Selector -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <form method="GET" action="{{ route('sales.daily') }}" class="flex items-center space-x-4">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Date:</label>
            <input type="date" name="date" value="{{ $date }}" 
                   class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                View Report
            </button>
            <a href="{{ route('sales.daily') }}?date={{ now()->format('Y-m-d') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg transition shadow">
                Today
            </a>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Sales</p>
            <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $summary['total_sales'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Revenue</p>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400">KSh {{ number_format($summary['total_revenue'], 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Tax Collected</p>
            <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">KSh {{ number_format($summary['total_tax'], 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Average Sale</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                KSh {{ $summary['total_sales'] > 0 ? number_format($summary['total_revenue'] / $summary['total_sales'], 2) : 0 }}
            </p>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Payment Methods</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400">Cash</span>
                    <span class="font-medium text-gray-800 dark:text-white">KSh {{ number_format($summary['cash'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400">Card</span>
                    <span class="font-medium text-gray-800 dark:text-white">KSh {{ number_format($summary['card'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400">Mobile Money</span>
                    <span class="font-medium text-gray-800 dark:text-white">KSh {{ number_format($summary['mobile_money'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400">Credit</span>
                    <span class="font-medium text-gray-800 dark:text-white">KSh {{ number_format($summary['credit'], 2) }}</span>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                    <div class="flex justify-between items-center font-bold">
                        <span class="text-gray-800 dark:text-white">Total</span>
                        <span class="text-green-600 dark:text-green-400">KSh {{ number_format($summary['total_revenue'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Hourly Breakdown</h3>
            @php
                $hourlyData = [];
                for ($i = 0; $i < 24; $i++) {
                    $hourlyData[$i] = $sales->filter(function($sale) use ($i) {
                        return $sale->created_at->format('H') == str_pad($i, 2, '0', STR_PAD_LEFT);
                    })->sum('total_amount');
                }
            @endphp
            <div class="space-y-2 max-h-60 overflow-y-auto">
                @foreach($hourlyData as $hour => $amount)
                    @if($amount > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00</span>
                            <span class="font-medium text-gray-800 dark:text-white">KSh {{ number_format($amount, 2) }}</span>
                        </div>
                    @endif
                @endforeach
                @if(array_sum($hourlyData) == 0)
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No sales data for this day</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Transactions</h3>
        
        <div class="overflow-x-auto">
            @if($sales->count() > 0)
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Time</th>
                            <th class="px-6 py-3">Invoice</th>
                            <th class="px-6 py-3">Items</th>
                            <th class="px-6 py-3">Payment</th>
                            <th class="px-6 py-3">Total</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4">{{ $sale->created_at->format('H:i') }}</td>
                                <td class="px-6 py-4 font-mono text-xs">{{ $sale->invoice_number }}</td>
                                <td class="px-6 py-4">{{ $sale->items->count() }}</td>
                                <td class="px-6 py-4 capitalize">{{ $sale->payment_method }}</td>
                                <td class="px-6 py-4 font-medium">KSh {{ number_format($sale->total_amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('sales.show', $sale) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-calendar-day text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">No sales found for this day</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
