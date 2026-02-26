@extends('layouts.app')

@section('title', 'All Activity Logs - Liquor Management System')

@section('page-title', 'Activity Logs')
@section('page-subtitle', 'View all system activity')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
    <!-- Filters -->
    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
        <form method="GET" action="{{ route('users.activity.all') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- User Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User</label>
                <select name="user_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Module Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Module</label>
                <select name="module" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Modules</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>{{ ucfirst($module) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Action</label>
                <select name="action" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Filter Buttons -->
            <div class="md:col-span-5 flex justify-end space-x-2 mt-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-filter mr-2"></i> Apply Filters
                </button>
                <a href="{{ route('users.activity.all') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg transition shadow">
                    <i class="fas fa-times mr-2"></i> Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="overflow-x-auto">
        @if($logs->count() > 0)
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">User</th>
                        <th class="px-6 py-3">Date/Time</th>
                        <th class="px-6 py-3">Action</th>
                        <th class="px-6 py-3">Module</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4">
                                @if($log->user)
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $log->user_name }}</div>
                                    <div class="text-xs text-gray-500">{!! $log->user_role ? '<span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full">' . ucfirst($log->user_role) . '</span>' : '' !!}</div>
                                @else
                                    <span class="text-gray-500">System</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $log->created_at->format('d M Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4">
                                {!! $log->action_badge !!}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                    {{ ucfirst($log->module) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                {{ $log->description ?? $log->module . ' ' . $log->action }}
                                @if($log->old_values || $log->new_values)
                                    <button onclick="showDetails({{ $log->id }})" class="ml-2 text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                    
                                    <!-- Hidden details div -->
                                    <div id="details-{{ $log->id }}" class="hidden mt-2 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg text-xs">
                                        @if($log->old_values)
                                            <div class="mb-2">
                                                <span class="font-semibold text-red-600">Old Values:</span>
                                                <pre class="mt-1 text-gray-700 dark:text-gray-300">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        @endif
                                        @if($log->new_values)
                                            <div>
                                                <span class="font-semibold text-green-600">New Values:</span>
                                                <pre class="mt-1 text-gray-700 dark:text-gray-300">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-xs">
                                {{ $log->ip_address ?? 'N/A' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="mb-4">
                    <i class="fas fa-history text-6xl text-gray-300 dark:text-gray-600"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No activity logs found</h4>
                <p class="text-gray-500 dark:text-gray-400">No activity matches your filters.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function showDetails(logId) {
        const detailsDiv = document.getElementById('details-' + logId);
        if (detailsDiv.classList.contains('hidden')) {
            detailsDiv.classList.remove('hidden');
        } else {
            detailsDiv.classList.add('hidden');
        }
    }
</script>
@endpush
@endsection
