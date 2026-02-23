<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'customer_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'amount_paid',
        'change',
        'payment_method',
        'payment_status',
        'status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the items for this sale.
     */
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get the user who created this sale.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the customer for this sale.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Generate a unique invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $last = self::where('invoice_number', 'like', "{$prefix}-{$date}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->invoice_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$date}-{$newNumber}";
    }

    /**
     * Get the payment status badge HTML.
     */
    public function getPaymentStatusBadgeAttribute(): string
    {
        $colors = [
            'paid' => 'bg-green-100 text-green-800',
            'partial' => 'bg-yellow-100 text-yellow-800',
            'pending' => 'bg-red-100 text-red-800',
        ];

        $color = $colors[$this->payment_status] ?? 'bg-gray-100 text-gray-800';
        
        return '<span class="px-2 py-1 text-xs font-medium ' . $color . ' rounded-full capitalize">' . str_replace('_', ' ', $this->payment_status) . '</span>';
    }

    /**
     * Get the status badge HTML.
     */
    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'completed' => 'bg-green-100 text-green-800',
            'void' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-orange-100 text-orange-800',
        ];

        $color = $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
        
        return '<span class="px-2 py-1 text-xs font-medium ' . $color . ' rounded-full capitalize">' . $this->status . '</span>';
    }

    /**
     * Scope a query to only include sales for a specific customer.
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope a query to only include sales within a date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include today's sales.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }

    /**
     * Scope a query to only include this week's sales.
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope a query to only include this month's sales.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }
}