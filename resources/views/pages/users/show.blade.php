@extends('layouts.app')

@section('title', $user->name . ' - User Details')

@section('page-title', $user->name)
@section('page-subtitle', 'User details and activity')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- User Details Card -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start mb-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $user->name }}</h2>
                        {!! $user->role_badge !!}
                        {!! $user->status_badge !!}
                        @if(auth()->id() === $user->id)
                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">You</span>
                        @endif
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">Employee ID: <span class="font-mono">{{ $user->employee_id ?? 'N/A' }}</span></p>
                </div>
                <div class="mt-4 md:mt-0 flex space-x-2">
                    <a href="{{ route('users.edit', $user) }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition shadow">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 text-center">
                    <p class="text-sm text-blue-600 dark:text-blue-400">Last Login</p>
                    <p class="text-xl font-bold text-blue-700 dark:text-blue-300">{{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Never' }}</p>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 text-center">
                    <p class="text-sm text-purple-600 dark:text-purple-400">Member Since</p>
                    <p class="text-xl font-bold text-purple-700 dark:text-purple-300">{{ $user->created_at->format('d M Y') }}</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 text-center">
                    <p class="text-sm text-green-600 dark:text-green-400">Activity Logs</p>
                    <p class="text-xl font-bold text-green-700 dark:text-green-300">{{ $user->activityLogs->count() }}</p>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Contact Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <div class="w-8 text-gray-500 dark:text-gray-400"><i class="fas fa-envelope"></i></div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                <p class="font-medium text-gray-800 dark:text-white">{{ $user->email }}</p>
                            </div>
                        </div>
                        
                        @if($user->phone)
                        <div class="flex items-start">
                            <div class="w-8 text-gray-500 dark:text-gray-400"><i class="fas fa-phone"></i></div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                                <p class="font-medium text-gray-800 dark:text-white">{{ $user->phone }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="flex items-start">
                            <div class="w-8 text-gray-500 dark:text-gray-400"><i class="fas fa-id-card"></i></div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Employee ID</p>
                                <p class="font-medium text-gray-800 dark:text-white">{{ $user->employee_id ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Account Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <div class="w-8 text-gray-500 dark:text-gray-400"><i class="fas fa-user-tag"></i></div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Role</p>
                                <p class="font-medium text-gray-800 dark:text-white">{!! $user->role_badge !!}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-8 text-gray-500 dark:text-gray-400"><i class="fas fa-circle"></i></div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                                <p class="font-medium text-gray-800 dark:text-white">{!! $user->status_badge !!}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-8 text-gray-500 dark:text-gray-400"><i class="fas fa-clock"></i></div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Last Login IP</p>
                                <p class="font-medium text-gray-800 dark:text-white">{{ $user->last_login_ip ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Logs Preview -->
            <div class="mt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Activity</h3>
                    <a href="{{ route('users.activity', $user) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                @if($user->activityLogs->count() > 0)
                    <div class="space-y-3">
                        @foreach($user->activityLogs->take(5) as $log)
                            <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="mr-3">
                                    {!! $log->action_badge !!}
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-800 dark:text-white">{{ $log->description ?? $log->module . ' ' . $log->action }}</p>
                                    <p class="text-xs text-gray-500">{{ $log->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No activity logs found</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('users.edit', $user) }}" class="flex items-center p-3 bg-yellow-50 dark:bg-yellow-900/30 hover:bg-yellow-100 dark:hover:bg-yellow-900/50 rounded-lg border border-yellow-200 dark:border-yellow-800 transition-colors group">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-edit text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div>
                        <span class="font-medium text-gray-800 dark:text-white">Edit User</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Update information</p>
                    </div>
                </a>

                <a href="{{ route('users.activity', $user) }}" class="flex items-center p-3 bg-purple-50 dark:bg-purple-900/30 hover:bg-purple-100 dark:hover:bg-purple-900/50 rounded-lg border border-purple-200 dark:border-purple-800 transition-colors group">
                    <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-history text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div>
                        <span class="font-medium text-gray-800 dark:text-white">View Activity</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">See all logs</p>
                    </div>
                </a>

                @if(auth()->id() !== $user->id)
                    <form action="{{ route('users.toggle-status', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center p-3 {{ $user->is_active ? 'bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50' : 'bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50' }} rounded-lg border {{ $user->is_active ? 'border-red-200 dark:border-red-800' : 'border-green-200 dark:border-green-800' }} transition-colors group">
                            <div class="w-10 h-10 rounded-full {{ $user->is_active ? 'bg-red-100 dark:bg-red-900' : 'bg-green-100 dark:bg-green-900' }} flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-{{ $user->is_active ? 'ban' : 'check-circle' }} {{ $user->is_active ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}"></i>
                            </div>
                            <div class="text-left">
                                <span class="font-medium text-gray-800 dark:text-white">{{ $user->is_active ? 'Deactivate' : 'Activate' }} User</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->is_active ? 'Disable login access' : 'Enable login access' }}</p>
                            </div>
                        </button>
                    </form>

                    @if(!($user->isAdmin() && App\Models\User::where('role', 'admin')->count() <= 1))
                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete user {{ $user->name }}? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full flex items-center p-3 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-lg border border-red-200 dark:border-red-800 transition-colors group">
                                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-trash text-red-600 dark:text-red-400"></i>
                                </div>
                                <div class="text-left">
                                    <span class="font-medium text-gray-800 dark:text-white">Delete User</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Permanently remove</p>
                                </div>
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>

        <!-- Created/Updated Info -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">System Info</h3>
            <div class="space-y-3 text-sm">
                @if($user->creator)
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Created By</p>
                    <p class="font-medium text-gray-800 dark:text-white">{{ $user->creator->name }}</p>
                    <p class="text-xs text-gray-500">{{ $user->created_at->format('d M Y H:i') }}</p>
                </div>
                @endif

                @if($user->updater)
                <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-gray-500 dark:text-gray-400">Last Updated By</p>
                    <p class="font-medium text-gray-800 dark:text-white">{{ $user->updater->name }}</p>
                    <p class="text-xs text-gray-500">{{ $user->updated_at->format('d M Y H:i') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
