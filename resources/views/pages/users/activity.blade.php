@extends('layouts.app')

@section('title', $user->name . ' - Activity Logs')

@section('page-title', $user->name . ' - Activity Logs')
@section('page-subtitle', 'View user activity history')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Activity Logs</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Showing logs for <span class="font-medium">{{ $user->name }}</span> ({!! $user->role_badge !!})
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <a href="{{ route('users.show', $user) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                <i class="fas fa-arrow-left mr-2"></i> Back to User
            </a>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="overflow-x-auto">
        @if($logs->count() > 0)
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
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
                <p class="text-gray-500 dark:text-gray-400">This user hasn't performed any actions yet.</p>
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
