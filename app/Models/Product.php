<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'alcohol_percentage' => 'decimal:2',
        'volume_ml' => 'integer',
        'stock_quantity' => 'integer',
        'min_stock_level' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'status' => 'active',
        'stock_quantity' => 0,
        'min_stock_level' => 10,
    ];

    // ─────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────────────────

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the supplier for this product.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get all stock movements for this product.
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // ─────────────────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────────────────

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive products.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to only include low stock products.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_level');
    }

    /**
     * Scope a query to only include out of stock products.
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    // ─────────────────────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────────────────────

    /**
     * Get the formatted price with currency symbol.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'KSh ' . number_format($this->price, 2);
    }

    /**
     * Get the formatted cost price.
     */
    public function getFormattedCostPriceAttribute(): string
    {
        return $this->cost_price ? 'KSh ' . number_format($this->cost_price, 2) : 'N/A';
    }

    /**
     * Check if product is in stock.
     */
    public function getInStockAttribute(): bool
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Check if product is low stock.
     */
    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    // ─────────────────────────────────────────────────────────
    // CUSTOM METHODS
    // ─────────────────────────────────────────────────────────

    /**
     * Generate a unique SKU.
     */
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
     * Update stock quantity and log the movement.
     *
     * @param int $quantity Change amount (positive for addition, negative for removal)
     * @param string $type Type of movement (purchase, sale, adjustment, return, damage, expired)
     * @param string|null $reference Optional reference (e.g., PO number, invoice)
     * @param string|null $reason Optional reason
     * @return bool Success or failure (e.g., would go below zero)
     */
    public function updateStock(int $quantity, string $type, ?string $reference = null, ?string $reason = null): bool
    {
        $old = $this->stock_quantity;
        $new = $old + $quantity;

        // Prevent negative stock
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