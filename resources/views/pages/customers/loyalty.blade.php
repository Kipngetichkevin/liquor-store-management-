@extends('layouts.app')

@section('title', 'Loyalty Dashboard - Liquor Management System')

@section('page-title', 'Loyalty Dashboard')
@section('page-subtitle', 'Customer loyalty analytics and insights')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-blue-100 mb-1">Total Points Issued</p>
            <p class="text-3xl font-bold">{{ number_format($totalPoints, 0) }}</p>
            <p class="text-blue-200 text-sm mt-2">across all customers</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-green-100 mb-1">Total Customer Spend</p>
            <p class="text-3xl font-bold">KSh {{ number_format($totalSpent, 0) }}</p>
            <p class="text-green-200 text-sm mt-2">lifetime value</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-purple-100 mb-1">Average Points per Customer</p>
            <p class="text-3xl font-bold">{{ $totalPoints > 0 && $tierStats ? number_format($totalPoints / array_sum($tierStats), 0) : 0 }}</p>
            <p class="text-purple-200 text-sm mt-2">per customer</p>
        </div>
    </div>

    <!-- Tier Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Tier Pie Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Customer Tiers</h3>
            <div class="h-64">
                <canvas id="tierChart"></canvas>
            </div>
        </div>

        <!-- Tier Stats Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Tier Breakdown</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-amber-500 mr-3"></div>
                        <span class="font-medium text-gray-800 dark:text-white">Bronze</span>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-gray-900 dark:text-white">{{ $tierStats['bronze'] }}</span>
                        <span class="text-sm text-gray-500 ml-2">customers</span>
                    </div>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-gray-400 mr-3"></div>
                        <span class="font-medium text-gray-800 dark:text-white">Silver</span>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-gray-900 dark:text-white">{{ $tierStats['silver'] }}</span>
                        <span class="text-sm text-gray-500 ml-2">customers</span>
                    </div>
                </div>
                <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-yellow-500 mr-3"></div>
                        <span class="font-medium text-gray-800 dark:text-white">Gold</span>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-gray-900 dark:text-white">{{ $tierStats['gold'] }}</span>
                        <span class="text-sm text-gray-500 ml-2">customers</span>
                    </div>
                </div>
                <div class="flex items-center justify-between p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-purple-500 mr-3"></div>
                        <span class="font-medium text-gray-800 dark:text-white">Platinum</span>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-gray-900 dark:text-white">{{ $tierStats['platinum'] }}</span>
                        <span class="text-sm text-gray-500 ml-2">customers</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Top Spenders -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">🏆 Top Spenders</h3>
            <div class="space-y-4">
                @forelse($topCustomers as $customer)
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-3 font-bold text-blue-600 dark:text-blue-400">
                                {{ $loop->iteration }}
                            </div>
                            <div>
                                <a href="{{ route('customers.show', $customer) }}" class="font-medium text-gray-800 dark:text-white hover:underline">
                                    {{ $customer->name }}
                                </a>
                                <div class="flex items-center text-xs text-gray-500 mt-1">
                                    <span class="mr-3">{!! $customer->tier_badge !!}</span>
                                    <span>{{ $customer->total_visits }} visits</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-gray-900 dark:text-white">KSh {{ number_format($customer->total_spent, 0) }}</div>
                            <div class="text-xs text-yellow-600">{{ number_format($customer->loyalty_points) }} pts</div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">No customers yet</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Joiners -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">🆕 Recent Joiners</h3>
            <div class="space-y-4">
                @forelse($recentJoins as $customer)
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center mr-3 text-green-600 dark:text-green-400">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div>
                                <a href="{{ route('customers.show', $customer) }}" class="font-medium text-gray-800 dark:text-white hover:underline">
                                    {{ $customer->name }}
                                </a>
                                <div class="text-xs text-gray-500 mt-1">
                                    Joined {{ $customer->member_since?->diffForHumans() ?? 'recently' }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            {!! $customer->tier_badge !!}
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">No customers yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('customers.create') }}" class="flex items-center justify-center p-4 bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-lg border border-green-200 dark:border-green-800 transition-colors">
                <i class="fas fa-user-plus text-green-600 dark:text-green-400 mr-2"></i>
                <span class="font-medium text-gray-800 dark:text-white">Add Customer</span>
            </a>
            <a href="{{ route('customers.index') }}" class="flex items-center justify-center p-4 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg border border-blue-200 dark:border-blue-800 transition-colors">
                <i class="fas fa-users text-blue-600 dark:text-blue-400 mr-2"></i>
                <span class="font-medium text-gray-800 dark:text-white">View All Customers</span>
            </a>
            <a href="{{ route('pos.index') }}" class="flex items-center justify-center p-4 bg-purple-50 dark:bg-purple-900/30 hover:bg-purple-100 dark:hover:bg-purple-900/50 rounded-lg border border-purple-200 dark:border-purple-800 transition-colors">
                <i class="fas fa-shopping-cart text-purple-600 dark:text-purple-400 mr-2"></i>
                <span class="font-medium text-gray-800 dark:text-white">New Sale</span>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('tierChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Bronze', 'Silver', 'Gold', 'Platinum'],
                datasets: [{
                    data: [
                        {{ $tierStats['bronze'] ?? 0 }},
                        {{ $tierStats['silver'] ?? 0 }},
                        {{ $tierStats['gold'] ?? 0 }},
                        {{ $tierStats['platinum'] ?? 0 }}
                    ],
                    backgroundColor: ['#F59E0B', '#9CA3AF', '#FBBF24', '#8B5CF6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12 }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
