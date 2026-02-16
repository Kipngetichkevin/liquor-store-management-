<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'supplier_code',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'tax_number',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'is_active' => 1,
    ];

    // ─────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────────────────

    /**
     * Get the products for this supplier.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // ─────────────────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────────────────

    /**
     * Scope a query to only include active suppliers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope a query to only include inactive suppliers.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', 0);
    }

    // ─────────────────────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────────────────────

    /**
     * Get the status badge HTML.
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active
            ? '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>'
            : '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full dark:bg-gray-700 dark:text-gray-300">Inactive</span>';
    }

    // ─────────────────────────────────────────────────────────
    // CUSTOM METHODS
    // ─────────────────────────────────────────────────────────

    /**
     * Generate a unique supplier code.
     */
    public static function generateSupplierCode(): string
    {
        $prefix = 'SUP';
        $year = now()->format('y');
        $month = now()->format('m');
        $last = self::where('supplier_code', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('supplier_code', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->supplier_code, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$year}{$month}-{$newNumber}";
    }
}