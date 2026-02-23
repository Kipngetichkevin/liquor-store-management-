@extends('layouts.app')

@section('title', 'Weekly Sales Report')

@section('page-title', 'Weekly Sales Report')
@section('page-subtitle', 'Detailed breakdown with profit and tax')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Date Range Selector -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <form method="GET" action="{{ route('sales.weekly') }}" class="flex flex-wrap items-center gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-search mr-2"></i> Generate
                </button>
                <a href="{{ route('sales.weekly.export-csv') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-file-csv mr-2"></i> Export to CSV
                </a>
                <a href="{{ route('sales.weekly') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg transition shadow">
                    This Week
                </a>
            </div>
        </form>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
            Showing: <span class="font-medium">{{ date('M d, Y', strtotime($startDate)) }}</span> to 
            <span class="font-medium">{{ date('M d, Y', strtotime($endDate)) }}</span>
        </p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Revenue</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">KSh {{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Profit</p>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400">KSh {{ number_format($totalProfit, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Tax</p>
            <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">KSh {{ number_format($totalTax, 2) }}</p>
        </div>
    </div>

    <!-- Daily Summary Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Daily Summary</h3>
        <div class="h-80">
            <canvas id="dailyChart"></canvas>
        </div>
    </div>

    <!-- Daily Summary Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Daily Breakdown</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Transactions</th>
                        <th class="px-6 py-3">Revenue</th>
                        <th class="px-6 py-3">Tax</th>
                        <th class="px-6 py-3">Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailySummary as $day)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4">{{ date('D, M d, Y', strtotime($day->date)) }}</td>
                            <td class="px-6 py-4">{{ $day->transactions }}</td>
                            <td class="px-6 py-4 font-medium">KSh {{ number_format($day->revenue, 2) }}</td>
                            <td class="px-6 py-4">KSh {{ number_format($day->tax, 2) }}</td>
                            <td class="px-6 py-4 text-green-600 dark:text-green-400">KSh {{ number_format($day->profit_before_tax, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                No sales data for this period
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detailed Items Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Detailed Sales Items</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Invoice</th>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Qty</th>
                        <th class="px-6 py-3">Selling Price</th>
                        <th class="px-6 py-3">Cost Price</th>
                        <th class="px-6 py-3">Profit/Unit</th>
                        <th class="px-6 py-3">Total Profit</th>
                        <th class="px-6 py-3">Tax</th>
                        <th class="px-6 py-3">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @php
                            $sellingPrice = $item->unit_price;
                            $costPrice = $item->product->cost_price ?? 0;
                            $profitPerUnit = $sellingPrice - $costPrice;
                            $totalProfit = $profitPerUnit * $item->quantity;
                            $saleTotal = $item->sale->total_amount;
                            $saleTax = $item->sale->tax_amount;
                            $itemTax = $saleTotal > 0 ? ($item->subtotal / $saleTotal) * $saleTax : 0;
                        @endphp
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-3">{{ date('d/m/Y', strtotime($item->sale->created_at)) }}</td>
                            <td class="px-6 py-3 font-mono text-xs">{{ $item->sale->invoice_number }}</td>
                            <td class="px-6 py-3">{{ $item->product->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-3">{{ $item->quantity }}</td>
                            <td class="px-6 py-3">KSh {{ number_format($sellingPrice, 2) }}</td>
                            <td class="px-6 py-3">KSh {{ number_format($costPrice, 2) }}</td>
                            <td class="px-6 py-3 text-green-600 dark:text-green-400">KSh {{ number_format($profitPerUnit, 2) }}</td>
                            <td class="px-6 py-3 text-green-600 dark:text-green-400">KSh {{ number_format($totalProfit, 2) }}</td>
                            <td class="px-6 py-3 text-yellow-600 dark:text-yellow-400">KSh {{ number_format($itemTax, 2) }}</td>
                            <td class="px-6 py-3 font-medium">KSh {{ number_format($totalProfit + $itemTax, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                No sales data for this period
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('dailyChart').getContext('2d');
        
        // Prepare chart data safely using JavaScript
        const dailyData = @json($dailySummary);
        
        const labels = dailyData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
        });
        
        const revenue = dailyData.map(item => parseFloat(item.revenue) || 0);
        const profit = dailyData.map(item => parseFloat(item.profit_before_tax) || 0);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Revenue',
                        data: revenue,
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Profit',
                        data: profit,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': KSh ' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'KSh ' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection