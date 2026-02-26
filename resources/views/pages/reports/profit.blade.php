@extends('layouts.app')

@section('title', 'Profit & Loss Report')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Profit & Loss Report</h1>
        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mt-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Dashboard</a>
            <span class="mx-2">/</span>
            <span>Profit</span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
            <i class="fas fa-sliders-h mr-2 text-purple-600"></i> Filter & Group
        </h3>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="group" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Group by</label>
                <select name="group" id="group"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="day" {{ $group == 'day' ? 'selected' : '' }}>Day</option>
                    <option value="week" {{ $group == 'week' ? 'selected' : '' }}>Week</option>
                    <option value="month" {{ $group == 'month' ? 'selected' : '' }}>Month</option>
                </select>
            </div>
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
            <div class="flex items-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-check mr-2"></i> Apply
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
            <i class="fas fa-chart-pie mr-2 text-yellow-600"></i> Summary
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Period</th>
                        <th class="px-6 py-3">Sales Count</th>
                        <th class="px-6 py-3">Revenue (KSh)</th>
                        <th class="px-6 py-3">Tax (KSh)</th>
                        <th class="px-6 py-3">Profit (KSh)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $row->period }}</td>
                        <td class="px-6 py-4">{{ $row->total_sales }}</td>
                        <td class="px-6 py-4">KSh {{ number_format($row->revenue, 2) }}</td>
                        <td class="px-6 py-4">KSh {{ number_format($row->tax, 2) }}</td>
                        <td class="px-6 py-4">KSh {{ number_format($row->profit_before_tax, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            No data for this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700 font-medium">
                    <tr>
                        <td class="px-6 py-4 text-gray-900 dark:text-white">Totals</td>
                        <td class="px-6 py-4">{{ $totals['sales'] }}</td>
                        <td class="px-6 py-4">KSh {{ number_format($totals['revenue'], 2) }}</td>
                        <td class="px-6 py-4">KSh {{ number_format($totals['tax'], 2) }}</td>
                        <td class="px-6 py-4">KSh {{ number_format($totals['profit'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection