@extends('layouts.app')

@section('title', 'Purchase Orders - Liquor Management System')

@section('page-title', 'Purchase Orders')
@section('page-subtitle', 'Manage supplier orders')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Filters Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Filters</h3>
            <form method="GET" action="{{ route('purchase-orders.index') }}">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="PO Number, Supplier...">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Ordered</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition shadow">Apply</button>
                    <a href="{{ route('purchase-orders.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg text-center transition shadow">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders List -->
    <div class="lg:col-span-3">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Orders</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <i class="fas fa-shopping-cart text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Draft</p>
                        <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['draft'] }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <i class="fas fa-pen text-gray-600 dark:text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Ordered</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['ordered'] }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Received</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['received'] }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header with Add Button -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 sm:mb-0">Purchase Orders</h3>
                @if(in_array(auth()->user()->role, ['admin', 'manager', 'stock_keeper']))
                    <a href="{{ route('purchase-orders.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center shadow">
                        <i class="fas fa-plus mr-2"></i> New Purchase Order
                    </a>
                @endif
            </div>

            <!-- Orders Table -->
            <div class="overflow-x-auto">
                @if($orders->count() > 0)
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">PO Number</th>
                                <th class="px-6 py-3">Supplier</th>
                                <th class="px-6 py-3">Order Date</th>
                                <th class="px-6 py-3">Expected</th>
                                <th class="px-6 py-3">Total</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-mono text-xs font-medium text-gray-900 dark:text-white">{{ $order->po_number }}</td>
                                    <td class="px-6 py-4">{{ $order->supplier->name }}</td>
                                    <td class="px-6 py-4">{{ $order->order_date->format('d M Y') }}</td>
                                    <td class="px-6 py-4">{{ $order->expected_date?->format('d M Y') ?? '—' }}</td>
                                    <td class="px-6 py-4 font-medium">{{ number_format($order->total, 2) }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'draft' => 'gray',
                                                'ordered' => 'yellow',
                                                'partial' => 'blue',
                                                'received' => 'green',
                                                'cancelled' => 'red',
                                            ];
                                            $color = $statusColors[$order->status] ?? 'gray';
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800 rounded-full dark:bg-{{ $color }}-900 dark:text-{{ $color }}-300 capitalize">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <!-- View – everyone who can access the page (admin, manager, stock keeper) -->
                                            <a href="{{ route('purchase-orders.show', $order) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Edit – admin, manager, stock keeper (but only if status allows) -->
                                            @if(in_array(auth()->user()->role, ['admin', 'manager', 'stock_keeper']) && in_array($order->status, ['draft', 'ordered']))
                                                <a href="{{ route('purchase-orders.edit', $order) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif

                                            <!-- Delete – only admin, and only if draft -->
                                            @if(auth()->user()->role === 'admin' && $order->status == 'draft')
                                                <form action="{{ route('purchase-orders.destroy', $order) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Delete this purchase order?')">
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
                    <div class="mt-6">{{ $orders->links() }}</div>
                @else
                    <div class="text-center py-12">
                        <div class="mb-4">
                            <i class="fas fa-shopping-cart text-6xl text-gray-300 dark:text-gray-600"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No purchase orders found</h4>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Create your first purchase order to start tracking supplier orders.</p>
                        @if(in_array(auth()->user()->role, ['admin', 'manager', 'stock_keeper']))
                            <a href="{{ route('purchase-orders.create') }}" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow">
                                <i class="fas fa-plus mr-2"></i> New Purchase Order
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection