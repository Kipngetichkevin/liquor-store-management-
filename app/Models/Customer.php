<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_code',
        'name',
        'email',
        'phone',
        'phone_2',
        'birth_date',
        'id_number',
        'gender',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'loyalty_points',
        'tier',
        'total_spent',
        'total_visits',
        'last_visit',
        'member_since',
        'sms_opt_in',
        'email_opt_in',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'last_visit' => 'date',
        'member_since' => 'date',
        'total_spent' => 'decimal:2',
        'loyalty_points' => 'integer',
        'total_visits' => 'integer',
        'sms_opt_in' => 'boolean',
        'email_opt_in' => 'boolean',
    ];

    protected $attributes = [
        'tier' => 'bronze',
        'loyalty_points' => 0,
        'total_spent' => 0,
        'total_visits' => 0,
        'country' => 'Kenya',
    ];

    /**
     * Get the sales for this customer.
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get the user who created this customer.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this customer.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active customers (with sales in last 6 months).
     */
    public function scopeActive($query)
    {
        return $query->where('last_visit', '>=', now()->subMonths(6));
    }

    /**
     * Scope a query to only include customers by tier.
     */
    public function scopeTier($query, $tier)
    {
        return $query->where('tier', $tier);
    }

    /**
     * Generate a unique customer code.
     */
    public static function generateCustomerCode(): string
    {
        $prefix = 'CUS';
        $year = now()->format('y');
        $month = now()->format('m');
        $last = self::where('customer_code', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('customer_code', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->customer_code, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$year}{$month}-{$newNumber}";
    }

    /**
     * Update customer tier based on total spent.
     */
    public function updateTier(): void
    {
        $tiers = [
            'platinum' => 100000, // KSh 100,000+
            'gold' => 50000,      // KSh 50,000 - 99,999
            'silver' => 10000,    // KSh 10,000 - 49,999
            'bronze' => 0,        // Below 10,000
        ];

        foreach ($tiers as $tier => $threshold) {
            if ($this->total_spent >= $threshold) {
                $this->tier = $tier;
                break;
            }
        }

        $this->saveQuietly();
    }

    /**
     * Add loyalty points based on purchase amount.
     */
    public function addLoyaltyPoints(float $amount): void
    {
        // 1 point per KSh 100 spent
        $points = floor($amount / 100);
        $this->loyalty_points += $points;
        $this->saveQuietly();
    }

    /**
     * Get discount percentage based on tier.
     */
    public function getDiscountPercentage(): int
    {
        return match($this->tier) {
            'platinum' => 10,
            'gold' => 7,
            'silver' => 5,
            default => 0,
        };
    }

    /**
     * Get tier badge HTML.
     */
    public function getTierBadgeAttribute(): string
    {
        $colors = [
            'bronze' => 'bg-amber-600',
            'silver' => 'bg-gray-400',
            'gold' => 'bg-yellow-500',
            'platinum' => 'bg-purple-600',
        ];

        $color = $colors[$this->tier] ?? 'bg-gray-500';
        
        return '<span class="px-2 py-1 text-xs font-medium text-white ' . $color . ' rounded-full capitalize">' . $this->tier . '</span>';
    }

    /**
     * Get full address attribute.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);
        
        return implode(', ', $parts) ?: 'N/A';
    }
}