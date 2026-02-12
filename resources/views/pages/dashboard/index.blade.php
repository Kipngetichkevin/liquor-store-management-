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

        <!-- Today's Sales Card (Placeholder) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-300 text-sm font-medium mb-1">Today's Sales</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white">${{ number_format($todayRevenue, 2) }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                        <span id="todaySales">{{ $todaySales }}</span> Transactions
                    </p>
                </div>
                <div class="p-4 bg-purple-100 dark:bg-purple-900 rounded-xl">
                    <i class="fas fa-shopping-cart text-purple-600 dark:text-purple-400 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <span class="text-gray-400 dark:text-gray-500 text-sm italic">Sales module coming soon</span>
            </div>
        </div>

        <!-- Low Stock Alert Card (Placeholder) -->
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
                <span class="text-gray-400 dark:text-gray-500 text-sm italic">Inventory module coming soon</span>
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

        <!-- Sales Trend Chart (Placeholder) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Sales Trend (Weekly)</h3>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <i class="fas fa-calendar-alt mr-1"></i> This Week
                </div>
            </div>
            <div class="h-80 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-line text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">Sales data will appear here</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Start making sales to see trends</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Quick Actions -->
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
            <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                <a href="#" class="text-blue-600 dark:text-blue-400 text-sm font-medium hover:underline flex items-center justify-center">
                    View All Activities
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('products.create') }}" class="flex flex-col items-center justify-center p-5 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-xl border border-blue-100 dark:border-blue-800 transition-colors group">
                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-plus text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <span class="font-medium text-gray-800 dark:text-white">Add Product</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">New liquor item</span>
                </a>

                <a href="{{ route('categories.create') }}" class="flex flex-col items-center justify-center p-5 bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-xl border border-green-100 dark:border-green-800 transition-colors group">
                    <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-tag text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <span class="font-medium text-gray-800 dark:text-white">Add Category</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">New category</span>
                </a>

                <a href="{{ route('products.index') }}" class="flex flex-col items-center justify-center p-5 bg-purple-50 dark:bg-purple-900/30 hover:bg-purple-100 dark:hover:bg-purple-900/50 rounded-xl border border-purple-100 dark:border-purple-800 transition-colors group">
                    <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-list text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                    <span class="font-medium text-gray-800 dark:text-white">View Products</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">All items</span>
                </a>

                <a href="#" class="flex flex-col items-center justify-center p-5 bg-yellow-50 dark:bg-yellow-900/30 hover:bg-yellow-100 dark:hover:bg-yellow-900/50 rounded-xl border border-yellow-100 dark:border-yellow-800 transition-colors group">
                    <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-chart-bar text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    </div>
                    <span class="font-medium text-gray-800 dark:text-white">Reports</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">View analytics</span>
                </a>
            </div>
            
            <!-- System Status -->
            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-3">System Status</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Database</span>
                        <span class="px-2 py-1 text-xs bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 rounded-full">Connected</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Products Module</span>
                        <span class="px-2 py-1 text-xs bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 rounded-full">Active</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Categories Module</span>
                        <span class="px-2 py-1 text-xs bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 rounded-full">Active</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Sales Module</span>
                        <span class="px-2 py-1 text-xs bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400 rounded-full">Coming Soon</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

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
            labels: @json($categoryNames),
            datasets: [{
                data: @json($categoryCounts),
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
                        font: {
                            size: 12
                        }
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

    // Change chart type
    window.changeChartType = function(type) {
        initCategoryChart(type);
        // Update button states
        document.querySelectorAll('#categoryChart').parentElement.previousElementSibling.querySelectorAll('button').forEach(btn => {
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
            
            // Show success message
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
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-0 ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} mr-3"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Initialize chart on page load
    document.addEventListener('DOMContentLoaded', function() {
        initCategoryChart('pie');
        
        // Auto-refresh dashboard every 30 seconds
        setInterval(() => {
            if (!document.hidden) {
                window.refreshDashboard();
            }
        }, 30000);
    });
</script>

<!-- Dark Mode Toggle Script (if using TailAdmin's dark mode) -->
<script>
    // Check for dark mode preference
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
    
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    /* Smooth transitions for dark mode */
    .transition-colors {
        transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
    }
    
    /* Card hover effects */
    .hover-lift:hover {
        transform: translateY(-4px);
        transition: transform 0.2s ease;
    }
</style>
@endsection