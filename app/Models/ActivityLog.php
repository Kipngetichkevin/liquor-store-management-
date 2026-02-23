<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'module',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the user that performed this activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include logs for a specific module.
     */
    public function scopeModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope a query to only include logs for a specific action.
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to only include logs within a date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get action badge HTML.
     */
    public function getActionBadgeAttribute(): string
    {
        $colors = [
            'create' => 'bg-green-100 text-green-800',
            'update' => 'bg-blue-100 text-blue-800',
            'delete' => 'bg-red-100 text-red-800',
            'login' => 'bg-purple-100 text-purple-800',
            'logout' => 'bg-gray-100 text-gray-800',
        ];

        $color = $colors[$this->action] ?? 'bg-gray-100 text-gray-800';
        
        return '<span class="px-2 py-1 text-xs font-medium ' . $color . ' rounded-full">' . ucfirst($this->action) . '</span>';
    }
}