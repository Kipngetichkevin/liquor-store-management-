@php $results = session('import_results'); @endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">📊 {{ ucfirst($results['period']) }}ly Analysis Results</h3>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <p class="text-blue-100 text-sm">Period Days</p>
            <p class="text-2xl font-bold">{{ $results['total_days'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-4 text-white">
            <p class="text-green-100 text-sm">Products</p>
            <p class="text-2xl font-bold">{{ $results['total_products'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-4 text-white">
            <p class="text-purple-100 text-sm">Revenue</p>
            <p class="text-2xl font-bold">KSh {{ number_format($results['total_revenue'], 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg p-4 text-white">
            <p class="text-yellow-100 text-sm">Profit</p>
            <p class="text-2xl font-bold">KSh {{ number_format($results['total_profit'], 0) }}</p>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Cost of Goods</p>
            <p class="text-xl font-bold text-red-600">KSh {{ number_format($results['total_cost'], 2) }}</p>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Tax (16%)</p>
            <p class="text-xl font-bold text-yellow-600">KSh {{ number_format($results['total_tax'], 2) }}</p>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Profit Margin</p>
            <p class="text-xl font-bold text-green-600">{{ number_format(($results['total_profit'] / $results['total_revenue']) * 100, 1) }}%</p>
        </div>
    </div>

    <!-- Monthly Summary Table -->
    @if(count($results['monthly_summary']) > 0)
        <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-3">📅 Monthly Breakdown</h4>
        <div class="overflow-x-auto mb-6">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-3 py-2">Month</th>
                        <th class="px-3 py-2 text-right">Days</th>
                        <th class="px-3 py-2 text-right">Revenue</th>
                        <th class="px-3 py-2 text-right">Cost</th>
                        <th class="px-3 py-2 text-right">Profit</th>
                        <th class="px-3 py-2 text-right">Tax</th>
                        <th class="px-3 py-2 text-right">Margin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results['monthly_summary'] as $month)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-3 py-2 font-medium">{{ $month['display_month'] }}</td>
                            <td class="px-3 py-2 text-right">{{ $month['days'] }}</td>
                            <td class="px-3 py-2 text-right">KSh {{ number_format($month['revenue'], 0) }}</td>
                            <td class="px-3 py-2 text-right">KSh {{ number_format($month['cost'], 0) }}</td>
                            <td class="px-3 py-2 text-right text-green-600">KSh {{ number_format($month['profit'], 0) }}</td>
                            <td class="px-3 py-2 text-right text-yellow-600">KSh {{ number_format($month['tax'], 0) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format(($month['profit'] / $month['revenue']) * 100, 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Product Summary Table -->
    @if(count($results['product_summary']) > 0)
        <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-3">📦 Product Performance</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-3 py-2">Product</th>
                        <th class="px-3 py-2 text-right">Sold</th>
                        <th class="px-3 py-2 text-right">Revenue</th>
                        <th class="px-3 py-2 text-right">Cost</th>
                        <th class="px-3 py-2 text-right">Profit</th>
                        <th class="px-3 py-2 text-right">Margin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results['product_summary'] as $product)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-3 py-2 font-medium">{{ $product['product'] }}</td>
                            <td class="px-3 py-2 text-right">{{ $product['sell'] }}</td>
                            <td class="px-3 py-2 text-right">KSh {{ number_format($product['revenue'], 0) }}</td>
                            <td class="px-3 py-2 text-right">KSh {{ number_format($product['cost'], 0) }}</td>
                            <td class="px-3 py-2 text-right {{ $product['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                KSh {{ number_format($product['profit'], 0) }}
                            </td>
                            <td class="px-3 py-2 text-right">
                                @if($product['revenue'] > 0)
                                    {{ number_format(($product['profit'] / $product['revenue']) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Recent Daily Data -->
    @if(count($results['daily_summary']) > 0)
        <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-3 mt-6">📆 Recent Days</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-3 py-2">Date</th>
                        <th class="px-3 py-2 text-right">Revenue</th>
                        <th class="px-3 py-2 text-right">Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results['daily_summary'] as $day)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-3 py-2">{{ $day['display_date'] }}</td>
                            <td class="px-3 py-2 text-right">KSh {{ number_format($day['revenue'], 0) }}</td>
                            <td class="px-3 py-2 text-right text-green-600">KSh {{ number_format($day['profit'], 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>