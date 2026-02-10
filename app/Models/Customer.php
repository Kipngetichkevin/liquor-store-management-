<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'customer_type',
        'credit_limit',
        'current_balance',
        'is_active'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Get all sales for the customer.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Check if customer has available credit.
     */
    public function hasAvailableCredit($amount): bool
    {
        if ($this->credit_limit <= 0) {
            return false;
        }
        return ($this->current_balance + $amount) <= $this->credit_limit;
    }
}