@extends('layouts.app')

@section('title', $customer->name . ' - Customer Details')

@section('page-title', $customer->name)
@section('page-subtitle', 'Customer details and purchase history')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Customer Details Card -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <!-- Header with Tier -->
            <div class="flex flex-col md:flex-row justify-between items-start mb-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $customer->name }}</h2>
                        {!! $customer->tier_badge !!}
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">Code: <span class="font-mono">{{ $customer->customer_code }}</span></p>
                </div>
                <div class="mt-4 md:mt-0 flex space-x-2">
                    <a href="{{ route('customers.edit', $customer) }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition shadow">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 text-center">
                    <p class="text-sm text-blue-600 dark:text-blue-400">Total Spent</p>
                    <p class="text-xl font-bold text-blue-700 dark:text-blue-300">KSh {{ number_format($customer->total_spent, 0) }}</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 text-center">
                    <p class="text-sm text-green-600 dark:text-green-400">Loyalty Points</p>
                    <p class="text-xl font-bold text-green-700 dark:text-green-300">{{ number_format($customer->loyalty_points) }}</p>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 text-center">
                    <p class="text-sm text-purple-600 dark:text-purple-400">Total Visits</p>
                    <p class="text-xl font-bold text-purple-700 dark:text-purple-300">{{ $customer->total_visits }}</p>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 text-center">
                    <p class="text-sm text-yellow-600 dark:text-yellow-400">Discount</p>
                    <p class="text-xl font-bold text-yellow-700 dark:text-yellow-300">{{ $customer->getDiscountPercentage() }}%</p>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Contact Information</h3>
                    <div class="space-y-3">
                        @if($customer->email)
                            <div class="flex items-start">
                                <div class="w-8 text-gray-500 dark:text-gray-400"><i class="fas fa-envelope"></i></div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $customer->email }}</p>
                                </div>
                            </div>
                        @endif
                        
                        @if($customer->phone)
                            <div class="flex items-start">
                                <div class="w-8 text-gray-500 dark:text-gray-400"><i class="fas fa-phone"></i></div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $customer->phone }}</p>
                                    @if($customer->phone_2)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $customer->phone_2 }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($customer->id_number)
                            <div class="flex items-start">
                                <div class="w-8 text-gray-500 dark:text-gray-400"><i class="fas fa-id-card"></i></div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">ID/Passport</p>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $customer->id_number }}</p>
                                </div>
                            </div>
                        @endif

                        @if($customer->birth_date)
                            <div class="flex items-start">
                                <div class="w-8 text-gray-500 dark:text-gray-400"><i class="fas fa-birthday-cake"></i></div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Birth Date</p>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $customer->birth_date->format('d M Y') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Address</h3>
                    <div class="space-y-3">
                        @if($customer->full_address !== 'N/A')
                            <div class="flex items-start">
                                <div class="w-8 text-gray-500 dark:text-gray-400"><i class="fas fa-map-marker-alt"></i></div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $customer->full_address }}</p>
                                </div>
                            </div>
                        @endif

                        @if($customer->member_since)
                            <div class="flex items-start">
                                <div class="w-8 text-gray-500 dark:text-gray-400"><i class="fas fa-calendar-check"></i></div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Member Since</p>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $customer->member_since->format('d M Y') }}</p>
                                </div>
                            </div>
                        @endif

                        @if($customer->last_visit)
                            <div class="flex items-start">
                                <div class="w-8 text-gray-500 dark:text-gray-400"><i class="fas fa-clock"></i></div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Last Visit</p>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $customer->last_visit->format('d M Y') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Preferences -->
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Preferences</h4>
                        <div class="space-y-2">
                            @if($customer->sms_opt_in)
                                <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                                    <i class="fas fa-check-circle mr-2"></i> SMS Promotions
                                </span>
                            @endif
                            @if($customer->email_opt_in)
                                <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                    <i class="fas fa-check-circle mr-2"></i> Email Promotions
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($customer->notes)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</h4>
                    <p class="text-gray-600 dark:text-gray-400">{{ $customer->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar - Stats & Quick Actions -->
    <div class="lg:col-span-1">
        <!-- Tier Benefits Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Tier Benefits</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400">Current Tier</span>
                    <span>{!! $customer->tier_badge !!}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400">Discount</span>
                    <span class="text-2xl font-bold text-green-600">{{ $customer->getDiscountPercentage() }}%</span>
                </div>
                
                @if($customer->tier == 'bronze')
                    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            <i class="fas fa-info-circle mr-2"></i>
                            Spend KSh {{ number_format(10000 - $customer->total_spent, 0) }} more to reach Silver tier (5% discount)
                        </p>
                    </div>
                @elseif($customer->tier == 'silver')
                    <div class="mt-4 p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <p class="text-sm text-purple-700 dark:text-purple-300">
                            <i class="fas fa-info-circle mr-2"></i>
                            Spend KSh {{ number_format(50000 - $customer->total_spent, 0) }} more to reach Gold tier (7% discount)
                        </p>
                    </div>
                @elseif($customer->tier == 'gold')
                    <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                            <i class="fas fa-info-circle mr-2"></i>
                            Spend KSh {{ number_format(100000 - $customer->total_spent, 0) }} more to reach Platinum tier (10% discount)
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('pos.index') }}?customer={{ $customer->id }}" class="flex items-center p-3 bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-lg border border-green-200 dark:border-green-800 transition-colors group">
                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-shopping-cart text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <span class="font-medium text-gray-800 dark:text-white">New Sale</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Start POS with this customer</p>
                    </div>
                </a>

                <a href="{{ route('customers.edit', $customer) }}" class="flex items-center p-3 bg-yellow-50 dark:bg-yellow-900/30 hover:bg-yellow-100 dark:hover:bg-yellow-900/50 rounded-lg border border-yellow-200 dark:border-yellow-800 transition-colors group">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-edit text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div>
                        <span class="font-medium text-gray-800 dark:text-white">Edit Customer</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Update details</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Purchase History -->
<div class="mt-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Purchase History</h3>

        @if($customer->sales->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Invoice</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Items</th>
                            <th class="px-6 py-3">Payment</th>
                            <th class="px-6 py-3">Total</th>
                            <th class="px-6 py-3">Points Earned</th>
                            <th class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customer->sales as $sale)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-mono text-xs">{{ $sale->invoice_number }}</td>
                                <td class="px-6 py-4">{{ $sale->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4">{{ $sale->items->count() }}</td>
                                <td class="px-6 py-4 capitalize">{{ $sale->payment_method }}</td>
                                <td class="px-6 py-4 font-medium">KSh {{ number_format($sale->total_amount, 0) }}</td>
                                <td class="px-6 py-4">{{ floor($sale->total_amount / 100) }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('sales.show', $sale) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-shopping-bag text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400">No purchase history yet</p>
                <a href="{{ route('pos.index') }}?customer={{ $customer->id }}" class="inline-flex items-center mt-3 text-blue-600 hover:underline">
                    <i class="fas fa-plus mr-1"></i> Create first sale
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
