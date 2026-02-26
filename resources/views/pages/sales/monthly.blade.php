@extends('layouts.app')

@section('title', 'Monthly Sales Report')

@section('page-title', 'Monthly Sales Report')
@section('page-subtitle', 'Simple overview of monthly performance')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Month Selector -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <form method="GET" action="{{ route('sales.monthly') }}" class="flex flex-wrap items-center gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month</label>
                <select name="month" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year</label>
                <select name="year" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach(range(date('Y') - 2, date('Y') + 1) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-search mr-2"></i> Generate
                </button>
                <a href="{{ route('sales.monthly.export-csv') }}?month={{ $month }}&year={{ $year }}" 
                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-file-csv mr-2"></i> Export
                </a>
                <a href="{{ route('sales.monthly') }}?month={{ date('n') }}&year={{ date('Y') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg transition shadow">
                    Current
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-blue-100 mb-1">Total Sales</p>
            <p class="text-3xl font-bold">{{ $summary['total_sales'] }}</p>
            <p class="text-blue-200 text-sm mt-2">transactions</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-green-100 mb-1">Revenue</p>
            <p class="text-3xl font-bold">KSh {{ number_format($summary['total_revenue'], 0) }}</p>
            <p class="text-green-200 text-sm mt-2">total sales value</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-yellow-100 mb-1">Tax</p>
            <p class="text-3xl font-bold">KSh {{ number_format($summary['total_tax'], 0) }}</p>
            <p class="text-yellow-200 text-sm mt-2">16% VAT</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-purple-100 mb-1">Profit</p>
            <p class="text-3xl font-bold">KSh {{ number_format($summary['total_profit'] ?? 0, 0) }}</p>
            <p class="text-purple-200 text-sm mt-2">after tax</p>
        </div>
    </div>

    <!-- Stacked Bar Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Daily Revenue Breakdown (Profit + Tax)</h3>
        <div class="h-96">
            <canvas id="dailyChart"></canvas>
        </div>
    </div>

    <!-- Weekly Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        @php
            $weeks = [1 => 'Week 1', 2 => 'Week 2', 3 => 'Week 3', 4 => 'Week 4', 5 => 'Week 5'];
            $weekData = [];
            
            foreach ($dailyBreakdown as $day) {
                $dayNum = (int)date('j', strtotime($day->date));
                $weekNum = ceil($dayNum / 7);
                if (!isset($weekData[$weekNum])) {
                    $weekData[$weekNum] = 0;
                }
                $weekData[$weekNum] += $day->revenue;
            }
        @endphp

        @foreach($weeks as $weekNum => $weekName)
            @if(isset($weekData[$weekNum]))
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $weekName }}</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">KSh {{ number_format($weekData[$weekNum], 0) }}</p>
                    <p class="text-xs text-green-600 mt-1">Days {{ ($weekNum-1)*7+1 }}-{{ min($weekNum*7, date('t', mktime(0,0,0,$month,1,$year))) }}</p>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Daily Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Daily Summary</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Day</th>
                        <th class="px-4 py-2 text-right">Sales</th>
                        <th class="px-4 py-2 text-right">Revenue</th>
                        <th class="px-4 py-2 text-right">Tax</th>
                        <th class="px-4 py-2 text-right">Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailyBreakdown as $day)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-4 py-2">{{ date('d M', strtotime($day->date)) }}</td>
                            <td class="px-4 py-2">{{ date('D', strtotime($day->date)) }}</td>
                            <td class="px-4 py-2 text-right">{{ $day->transactions }}</td>
                            <td class="px-4 py-2 text-right font-medium">KSh {{ number_format($day->revenue, 0) }}</td>
                            <td class="px-4 py-2 text-right">KSh {{ number_format($day->tax, 0) }}</td>
                            <td class="px-4 py-2 text-right text-green-600">KSh {{ number_format($day->profit_before_tax, 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No sales data for this month
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
        
        const dailyLabels = @json($dailyBreakdown->pluck('date')->map(function($date) {
            return date('d M', strtotime($date));
        })->filter());
        
        const profitData = @json($dailyBreakdown->pluck('profit_before_tax')->map(function($value) {
            return floatval($value);
        }));
        
        const taxData = @json($dailyBreakdown->pluck('tax')->map(function($value) {
            return floatval($value);
        }));
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dailyLabels,
                datasets: [
                    {
                        label: 'Profit',
                        data: profitData,
                        backgroundColor: '#10B981', // Green
                        stack: 'Stack 0'
                    },
                    {
                        label: 'Tax',
                        data: taxData,
                        backgroundColor: '#F59E0B', // Orange
                        stack: 'Stack 0'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { 
                            boxWidth: 12,
                            usePointStyle: true,
                            pointStyle: 'rectRounded'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': KSh ' + context.parsed.y.toFixed(0);
                            },
                            footer: function(tooltipItems) {
                                let total = tooltipItems.reduce((sum, item) => sum + item.parsed.y, 0);
                                return 'Total: KSh ' + total.toFixed(0);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false }
                    },
                    y: { 
                        stacked: true,
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
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