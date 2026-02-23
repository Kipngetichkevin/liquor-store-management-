<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = User::query();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('employee_id', 'like', "%{$search}%");
                });
            }

            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            if ($request->filled('status')) {
                $query->where('is_active', $request->status === 'active');
            }

            $sortField = $request->get('sort', 'created_at');
            $sortDirection = $request->get('direction', 'desc');
            
            $allowedSortFields = ['created_at', 'name', 'email', 'role', 'last_login_at'];
            if (!in_array($sortField, $allowedSortFields)) {
                $sortField = 'created_at';
            }
            
            $query->orderBy($sortField, $sortDirection);

            $users = $query->paginate(15);

            $stats = [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'inactive' => User::where('is_active', false)->count(),
                'admin' => User::where('role', 'admin')->count(),
                'manager' => User::where('role', 'manager')->count(),
                'cashier' => User::where('role', 'cashier')->count(),
                'stock_keeper' => User::where('role', 'stock_keeper')->count(),
            ];

            $roles = User::getRoles();

            $breadcrumbs = [
                ['title' => 'Dashboard', 'url' => route('dashboard')],
                ['title' => 'Users', 'url' => route('users.index')],
            ];

            return view('pages.users.index', compact('users', 'stats', 'roles', 'breadcrumbs'));

        } catch (\Exception $e) {
            Log::error('User index error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load users. Please try again.');
        }
    }

    public function create()
    {
        $roles = User::getRoles();
        $employeeId = 'EMP-' . date('Y') . '-' . str_pad(User::count() + 1, 4, '0', STR_PAD_LEFT);

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Users', 'url' => route('users.index')],
            ['title' => 'Add User', 'url' => route('users.create')],
        ];

        return view('pages.users.create', compact('roles', 'employeeId', 'breadcrumbs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,cashier,stock_keeper',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'nullable|string|unique:users,employee_id',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);
        
        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
        }

        try {
            User::create($validated);
            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create user. Please try again.');
        }
    }

    public function show(User $user)
    {
        $user->load(['activityLogs' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(50);
        }]);

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Users', 'url' => route('users.index')],
            ['title' => $user->name, 'url' => route('users.show', $user)],
        ];

        return view('pages.users.show', compact('user', 'breadcrumbs'));
    }

    public function edit(User $user)
    {
        if (auth()->id() === $user->id && !auth()->user()->isAdmin()) {
            return back()->with('error', 'You cannot edit your own account with current permissions.');
        }

        $roles = User::getRoles();

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Users', 'url' => route('users.index')],
            ['title' => 'Edit User', 'url' => route('users.edit', $user)],
        ];

        return view('pages.users.edit', compact('user', 'roles', 'breadcrumbs'));
    }

    public function update(Request $request, User $user)
    {
        if (auth()->id() === $user->id && !auth()->user()->isAdmin()) {
            return back()->with('error', 'You cannot edit your own account with current permissions.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,manager,cashier,stock_keeper',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'nullable|string|unique:users,employee_id,' . $user->id,
            'is_active' => 'nullable|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active', $user->is_active);

        if (auth()->check()) {
            $validated['updated_by'] = auth()->id();
        }

        try {
            $user->update($validated);
            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            Log::error('User update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update user. Please try again.');
        }
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the last admin user.');
        }

        try {
            $user->delete();
            return redirect()->route('users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            Log::error('User deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete user.');
        }
    }

    public function toggleStatus(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot change your own status.');
        }

        if ($user->isAdmin() && User::where('role', 'admin')->where('is_active', true)->count() <= 1) {
            return back()->with('error', 'Cannot deactivate the last active admin.');
        }

        try {
            $newStatus = !$user->is_active;
            $user->update(['is_active' => $newStatus]);
            $statusText = $newStatus ? 'activated' : 'deactivated';
            return redirect()->route('users.index')->with('success', "User {$statusText} successfully.");
        } catch (\Exception $e) {
            Log::error('User status toggle failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to change user status.');
        }
    }

    public function activity(User $user)
    {
        $logs = $user->activityLogs()->orderBy('created_at', 'desc')->paginate(50);

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Users', 'url' => route('users.index')],
            ['title' => $user->name, 'url' => route('users.show', $user)],
            ['title' => 'Activity Logs', 'url' => route('users.activity', $user)],
        ];

        return view('pages.users.activity', compact('user', 'logs', 'breadcrumbs'));
    }

    public function allActivity(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);
        
        $users = User::orderBy('name')->get(['id', 'name']);
        $modules = ActivityLog::distinct('module')->pluck('module');
        $actions = ActivityLog::distinct('action')->pluck('action');

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Activity Logs', 'url' => route('users.activity.all')],
        ];

        return view('pages.users.all-activity', compact('logs', 'users', 'modules', 'actions', 'breadcrumbs'));
    }

    public function profile()
    {
        $user = auth()->user();
        
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'My Profile', 'url' => route('users.profile')],
        ];

        return view('pages.users.profile', compact('user', 'breadcrumbs'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:new_password|current_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
        ];

        if (!empty($validated['new_password'])) {
            $data['password'] = Hash::make($validated['new_password']);
        }

        try {
            $user->update($data);
            return redirect()->route('users.profile')->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            Log::error('Profile update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update profile.');
        }
    }
}