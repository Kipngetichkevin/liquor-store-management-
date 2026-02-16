<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'cost_price',
        'category_id',
        'status',
        'alcohol_percentage',
        'volume_ml',
        'sku',
        'barcode',
        'brand',
        'image',
        'stock_quantity',
        'min_stock_level',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'alcohol_percentage' => 'decimal:2',
        'volume_ml' => 'integer',
        'stock_quantity' => 'integer',
        'min_stock_level' => 'integer',
    ];

    protected $attributes = [
        'status' => 'active',
        'stock_quantity' => 0,
        'min_stock_level' => 10,
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_level');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return 'KSh ' . number_format($this->price, 2);
    }

    public function getFormattedCostPriceAttribute(): string
    {
        return $this->cost_price ? 'KSh ' . number_format($this->cost_price, 2) : 'N/A';
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    // Custom methods
    public static function generateSku(): string
    {
        $prefix = 'PRD';
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(substr(uniqid(), -4));
        $sku = $prefix . '-' . $timestamp . '-' . $random;

        while (self::where('sku', $sku)->exists()) {
            $sku = $prefix . '-' . now()->format('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));
        }

        return $sku;
    }

    /**
     * Update stock quantity and log the movement
     */
    public function updateStock(int $quantity, string $type, ?string $reference = null, ?string $reason = null): bool
    {
        $old = $this->stock_quantity;
        $new = $old + $quantity;

        if ($new < 0) {
            return false;
        }

        $this->stock_quantity = $new;
        $this->save();

        $this->stockMovements()->create([
            'user_id' => auth()->id(),
            'quantity_change' => $quantity,
            'new_quantity' => $new,
            'type' => $type,
            'reference' => $reference,
            'reason' => $reason,
        ]);

        return true;
    }
}