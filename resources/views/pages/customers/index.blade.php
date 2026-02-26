@extends('layouts.app')

@section('title', 'Customers - Liquor Management System')

@section('page-title', 'Customers')
@section('page-subtitle', 'Manage your customers and loyalty program')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Filters Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Filters</h3>
            
            <form method="GET" action="{{ route('customers.index') }}">
                <!-- Search -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Name, email, phone...">
                </div>

                <!-- Tier Filter -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tier</label>
                    <select name="tier" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Tiers</option>
                        <option value="bronze" {{ request('tier') == 'bronze' ? 'selected' : '' }}>Bronze</option>
                        <option value="silver" {{ request('tier') == 'silver' ? 'selected' : '' }}>Silver</option>
                        <option value="gold" {{ request('tier') == 'gold' ? 'selected' : '' }}>Gold</option>
                        <option value="platinum" {{ request('tier') == 'platinum' ? 'selected' : '' }}>Platinum</option>
                    </select>
                </div>

                <!-- Active Filter -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="active" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All</option>
                        <option value="active" {{ request('active') == 'active' ? 'selected' : '' }}>Active (last 6 months)</option>
                    </select>
                </div>

                <!-- Sort -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sort By</label>
                    <select name="sort" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>Date Added</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="total_spent" {{ request('sort') == 'total_spent' ? 'selected' : '' }}>Total Spent</option>
                        <option value="loyalty_points" {{ request('sort') == 'loyalty_points' ? 'selected' : '' }}>Loyalty Points</option>
                    </select>
                    
                    <select name="direction" class="w-full mt-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="desc" {{ request('direction', 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition shadow">
                        Apply Filters
                    </button>
                    <a href="{{ route('customers.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg text-center transition shadow">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Customers Grid -->
    <div class="lg:col-span-3">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Customers</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Active</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['active'] }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Spent</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">KSh {{ number_format($stats['total_spent'], 0) }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <i class="fas fa-coins text-purple-600 dark:text-purple-400"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Loyalty Points</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($stats['total_points'], 0) }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <i class="fas fa-star text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header with Action Buttons -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 sm:mb-0">Customer List</h3>
                <div class="flex space-x-3">
                    @if(in_array(auth()->user()->role, ['admin', 'manager']))
                        <a href="{{ route('customers.loyalty') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center shadow">
                            <i class="fas fa-chart-line mr-2"></i> Loyalty Dashboard
                        </a>
                    @endif
                    @if(in_array(auth()->user()->role, ['admin', 'manager', 'cashier']))
                        <a href="{{ route('customers.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center shadow">
                            <i class="fas fa-plus mr-2"></i> Add Customer
                        </a>
                    @endif
                </div>
            </div>

            <!-- Customers Table -->
            <div class="overflow-x-auto">
                @if($customers->count() > 0)
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Code</th>
                                <th class="px-6 py-3">Name</th>
                                <th class="px-6 py-3">Contact</th>
                                <th class="px-6 py-3">Tier</th>
                                <th class="px-6 py-3">Points</th>
                                <th class="px-6 py-3">Total Spent</th>
                                <th class="px-6 py-3">Last Visit</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-mono text-xs">{{ $customer->customer_code }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $customer->name }}</td>
                                    <td class="px-6 py-4">
                                        @if($customer->email)
                                            <div class="text-xs">{{ $customer->email }}</div>
                                        @endif
                                        @if($customer->phone)
                                            <div class="text-xs text-gray-500">{{ $customer->phone }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">{!! $customer->tier_badge !!}</td>
                                    <td class="px-6 py-4">{{ number_format($customer->loyalty_points) }}</td>
                                    <td class="px-6 py-4 font-medium">KSh {{ number_format($customer->total_spent, 0) }}</td>
                                    <td class="px-6 py-4">{{ $customer->last_visit ? $customer->last_visit->format('d M Y') : 'Never' }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <!-- View – everyone can view -->
                                            <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Edit – admin, manager, cashier -->
                                            @if(in_array(auth()->user()->role, ['admin', 'manager', 'cashier']))
                                                <a href="{{ route('customers.edit', $customer) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif

                                            <!-- Delete – only admin -->
                                            @if(auth()->user()->role === 'admin' && $customer->sales()->count() == 0)
                                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Delete this customer?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $customers->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="mb-4">
                            <i class="fas fa-users text-6xl text-gray-300 dark:text-gray-600"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No customers found</h4>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Add your first customer to start tracking loyalty.</p>
                        @if(in_array(auth()->user()->role, ['admin', 'manager', 'cashier']))
                            <a href="{{ route('customers.create') }}" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow">
                                <i class="fas fa-plus mr-2"></i> Add Customer
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection