<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'supplier_id',
        'user_id',
        'purchase_date',
        'total_amount',
        'status',
        'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'purchase_date' => 'date'
    ];

    /**
     * Get the supplier that owns the purchase.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user that made the purchase.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}