@extends('layouts.app')

@section('title', 'Dashboard - Liquor Management System')

@section('content')
<!-- Dashboard Content -->
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard Overview</h1>
                <p class="text-gray-600 dark:text-gray-300">Welcome to your liquor management system dashboard</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Last updated: {{ now()->format('g:i A') }}</span>
                <button onclick="refreshDashboard()" class="p-2 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Products Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-300 text-sm font-medium mb-1">Total Products</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white" id="totalProducts">{{ $totalProducts }}</h3>
                    <p class="text-green-600 dark:text-green-400 text-sm mt-1">
                        <i class="fas fa-arrow-up mr-1"></i> 
                        <span id="activeProducts">{{ $activeProducts }}</span> Active
                    </p>
                </div>
                <div class="p-4 bg-blue-100 dark:bg-blue-900 rounded-xl">
                    <i class="fas fa-wine-bottle text-blue-600 dark:text-blue-400 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('products.index') }}" class="text-blue-600 dark:text-blue-400 text-sm font-medium hover:underline flex items-center">
                    View All Products
                    <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
        </div>

        <!-- Categories Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-300 text-sm font-medium mb-1">Categories</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalCategories }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Product groups</p>
                </div>
                <div class="p-4 bg-green-100 dark:bg-green-900 rounded-xl">
                    <i class="fas fa-tags text-green-600 dark:text-green-400 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('categories.index') }}" class="text-green-600 dark:text-green-400 text-sm font-medium hover:underline flex items-center">
                    Manage Categories
                    <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
        </div>

        <!-- Today's Sales Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-300 text-sm font-medium mb-1">Today's Sales</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white">KSh {{ number_format($todayRevenue, 2) }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                        <span id="todaySales">{{ $todaySales }}</span> Transactions
                    </p>
                </div>
                <div class="p-4 bg-purple-100 dark:bg-purple-900 rounded-xl">
                    <i class="fas fa-shopping-cart text-purple-600 dark:text-purple-400 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('pos.index') }}" class="text-purple-600 dark:text-purple-400 text-sm font-medium hover:underline flex items-center">
                    New Sale
                    <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
        </div>

        <!-- Low Stock Alert Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-300 text-sm font-medium mb-1">Low Stock Items</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalLowStock }}</h3>
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Needs attention
                    </p>
                </div>
                <div class="p-4 bg-red-100 dark:bg-red-900 rounded-xl">
                    <i class="fas fa-exclamation text-red-600 dark:text-red-400 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('inventory.index') }}" class="text-red-600 dark:text-red-400 text-sm font-medium hover:underline flex items-center">
                    View Inventory
                    <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Products by Category Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Products by Category</h3>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 text-sm bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-lg" onclick="changeChartType('pie')">
                        Pie
                    </button>
                    <button class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg" onclick="changeChartType('bar')">
                        Bar
                    </button>
                </div>
            </div>
            <div class="h-80">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <!-- Sales Trend Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Sales Trend (This Week)</h3>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <i class="fas fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::now()->startOfWeek()->format('M d') }} - {{ \Carbon\Carbon::now()->endOfWeek()->format('M d, Y') }}
                </div>
            </div>
            <div class="h-80">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Top Products -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Activities -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6">Recent Activities</h3>
            <div class="space-y-4">
                @forelse($activities as $activity)
                <div class="flex items-start p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $activity['color'] }}">
                            <i class="{{ $activity['icon'] }}"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="font-medium text-gray-800 dark:text-white">
                            {{ ucfirst($activity['title']) }} {{ $activity['action'] }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $activity['subtitle'] }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['time'] }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-history text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">No recent activities</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6">Top Selling Products</h3>
            <div class="space-y-4">
                @forelse($topProducts ?? [] as $item)
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-3">
                            <i class="fas fa-wine-bottle text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">{{ $item->product->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->total_quantity }} units sold</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-800 dark:text-white">KSh {{ number_format($item->total_revenue, 2) }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-chart-line text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">No sales yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Chart colors for dark/light mode
    const getChartColors = () => {
        const isDark = document.documentElement.classList.contains('dark');
        return {
            backgroundColors: [
                '#3B82F6', '#10B981', '#8B5CF6', '#F59E0B', '#EF4444',
                '#EC4899', '#14B8A6', '#F97316', '#84CC16', '#06B6D4'
            ],
            borderColor: isDark ? '#374151' : '#E5E7EB',
            textColor: isDark ? '#D1D5DB' : '#374151'
        };
    };

    // Initialize Category Chart
    let categoryChart;
    const initCategoryChart = (type = 'pie') => {
        const ctx = document.getElementById('categoryChart').getContext('2d');
        const colors = getChartColors();
        
        const data = {
            labels: @json($categoryNames ?? []),
            datasets: [{
                data: @json($categoryCounts ?? []),
                backgroundColor: colors.backgroundColors,
                borderColor: colors.borderColor,
                borderWidth: 1
            }]
        };
        
        const options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: colors.textColor,
                        padding: 20,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed + ' products';
                            return label;
                        }
                    }
                }
            }
        };
        
        if (type === 'bar') {
            options.indexAxis = 'y';
            options.plugins.legend.display = false;
        }
        
        if (categoryChart) {
            categoryChart.destroy();
        }
        
        categoryChart = new Chart(ctx, {
            type: type,
            data: data,
            options: options
        });
    };

    // Initialize Sales Chart
    let salesChart;
    const initSalesChart = () => {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const colors = getChartColors();
        
        const salesData = @json($weeklySales ?? []);
        const labels = salesData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { weekday: 'short' });
        });
        const revenues = salesData.map(item => item.revenue);
        
        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue (KSh)',
                    data: revenues,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'KSh ' + context.parsed.y.toFixed(2);
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
    };

    // Change chart type
    window.changeChartType = function(type) {
        initCategoryChart(type);
        // Update button states
        const buttons = document.querySelectorAll('#categoryChart').parentElement.previousElementSibling.querySelectorAll('button');
        buttons.forEach(btn => {
            if (btn.textContent.toLowerCase() === type) {
                btn.className = 'px-3 py-1 text-sm bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-lg';
            } else {
                btn.className = 'px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg';
            }
        });
    };

    // Refresh dashboard data
    window.refreshDashboard = async function() {
        const refreshBtn = event.target.closest('button');
        refreshBtn.classList.add('animate-spin');
        
        try {
            const response = await fetch('/dashboard/stats');
            const data = await response.json();
            
            // Update counts
            document.getElementById('totalProducts').textContent = data.totalProducts;
            document.getElementById('activeProducts').textContent = data.activeProducts;
            document.getElementById('todaySales').textContent = data.todaySales;
            
            // Update time
            document.querySelector('span.text-gray-500').textContent = `Last updated: ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;
            
            showNotification('Dashboard updated successfully', 'success');
        } catch (error) {
            showNotification('Failed to update dashboard', 'error');
        } finally {
            setTimeout(() => {
                refreshBtn.classList.remove('animate-spin');
            }, 500);
        }
    };

    // Show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-0 ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} mr-3"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Initialize charts on page load
    document.addEventListener('DOMContentLoaded', function() {
        initCategoryChart('pie');
        initSalesChart();
        
        // Auto-refresh dashboard every 30 seconds
        setInterval(() => {
            if (!document.hidden) {
                window.refreshDashboard();
            }
        }, 30000);
    });
</script>

<!-- Dark Mode Toggle Script -->
<script>
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
</script>

<style>
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .transition-colors {
        transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
    }
</style>
@endsection