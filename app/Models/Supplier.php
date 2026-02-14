<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_code',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'tax_number',
        'status',
        'is_active',
    ];

    protected $attributes = [
        'status' => 'active',
        'is_active' => 1,
    ];

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

    public function getStatusBadgeAttribute(): string
    {
        $status = $this->status ?? ($this->is_active ? 'active' : 'inactive');
        return $status === 'active'
            ? '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>'
            : '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full dark:bg-gray-700 dark:text-gray-300">Inactive</span>';
    }
}
