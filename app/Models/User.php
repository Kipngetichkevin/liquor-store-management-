<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'phone',
        'employee_id',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    protected $attributes = [
        'role' => 'cashier',
        'is_active' => true,
    ];

    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_CASHIER = 'cashier';
    const ROLE_STOCK_KEEPER = 'stock_keeper';

    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_CASHIER => 'Cashier',
            self::ROLE_STOCK_KEEPER => 'Stock Keeper',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isCashier(): bool
    {
        return $this->role === self::ROLE_CASHIER;
    }

    public function isStockKeeper(): bool
    {
        return $this->role === self::ROLE_STOCK_KEEPER;
    }

    public function getRoleBadgeAttribute(): string
    {
        $colors = [
            self::ROLE_ADMIN => 'bg-red-100 text-red-800',
            self::ROLE_MANAGER => 'bg-purple-100 text-purple-800',
            self::ROLE_CASHIER => 'bg-green-100 text-green-800',
            self::ROLE_STOCK_KEEPER => 'bg-blue-100 text-blue-800',
        ];

        $color = $colors[$this->role] ?? 'bg-gray-100 text-gray-800';
        $roleName = self::getRoles()[$this->role] ?? ucfirst($this->role);
        
        return '<span class="px-2 py-1 text-xs font-medium ' . $color . ' rounded-full">' . $roleName . '</span>';
    }

    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_active) {
            return '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>';
        }
        return '<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Inactive</span>';
    }

    public function logActivity(string $action, string $module, ?string $description = null, ?array $oldValues = null, ?array $newValues = null): void
    {
        ActivityLog::create([
            'user_id' => $this->id,
            'user_name' => $this->name,
            'user_role' => $this->role,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}