@extends('layouts.app')

@section('title', 'Sales Reports - Liquor Management System')

@section('page-title', 'Sales Reports')
@section('page-subtitle', 'View and filter all sales transactions')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Filters Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Filter Sales</h3>
            <form method="GET" action="{{ route('sales.index') }}">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Invoice</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Invoice number...">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
                    <select name="payment_method" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Methods</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                        <option value="mobile_money" {{ request('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="credit" {{ request('payment_method') == 'credit' ? 'selected' : '' }}>Credit</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition shadow">
                        Apply Filters
                    </button>
                    <a href="{{ route('sales.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg text-center transition shadow">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Quick Reports -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Reports</h3>
            <div class="space-y-2">
                <a href="{{ route('sales.daily') }}" class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors">
                    <i class="fas fa-calendar-day text-blue-600 dark:text-blue-400 mr-3"></i>
                    <span class="font-medium text-gray-800 dark:text-white">Daily Report</span>
                </a>
                <a href="{{ route('sales.monthly') }}" class="flex items-center p-3 bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-lg transition-colors">
                    <i class="fas fa-calendar-alt text-green-600 dark:text-green-400 mr-3"></i>
                    <span class="font-medium text-gray-800 dark:text-white">Monthly Report</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Sales List -->
    <div class="lg:col-span-3">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Sales</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalSales }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <i class="fas fa-shopping-cart text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">KSh {{ number_format($totalRevenue, 2) }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <i class="fas fa-coins text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Tax</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">KSh {{ number_format($totalTax, 2) }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <i class="fas fa-percent text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Sales Transactions</h3>
            
            <div class="overflow-x-auto">
                @if($sales->count() > 0)
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Invoice</th>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Cashier</th>
                                <th class="px-6 py-3">Items</th>
                                <th class="px-6 py-3">Total</th>
                                <th class="px-6 py-3">Payment</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-mono text-xs font-medium text-gray-900 dark:text-white">
                                        {{ $sale->invoice_number }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $sale->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $sale->user->name ?? 'System' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $sale->items->count() }}
                                    </td>
                                    <td class="px-6 py-4 font-medium">
                                        KSh {{ number_format($sale->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 capitalize">
                                        {{ $sale->payment_method }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'completed' => 'green',
                                                'pending' => 'yellow',
                                                'cancelled' => 'red',
                                            ];
                                            $color = $statusColors[$sale->status] ?? 'gray';
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800 rounded-full dark:bg-{{ $color }}-900 dark:text-{{ $color }}-300 capitalize">
                                            {{ $sale->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('sales.show', $sale) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('sales.print', $sale) }}" target="_blank" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300" title="Print">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $sales->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-shopping-cart text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-lg">No sales found</p>
                        <p class="text-gray-400 dark:text-gray-500 mt-2">Try adjusting your filters or make a sale in POS</p>
                        <a href="{{ route('pos.index') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                            <i class="fas fa-plus mr-2"></i> New Sale
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
