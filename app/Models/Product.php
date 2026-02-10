<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'name',
        'sku',
        'barcode',
        'description',
        'price',
        'cost_price',
        'quantity',
        'min_stock_level',
        'bottle_size',
        'alcohol_percentage',
        'brand',
        'country_of_origin',
        'expiry_date',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'alcohol_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'quantity' => 'integer',
        'min_stock_level' => 'integer',
        'expiry_date' => 'date',
    ];

    // Automatically generate SKU if not provided
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = 'PROD-' . strtoupper(Str::random(8));
            }
        });
    }

    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship with Supplier (if exists)
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Check if stock is low
    public function isLowStock()
    {
        return $this->quantity <= $this->min_stock_level;
    }

    // Get profit margin
    public function getProfitMarginAttribute()
    {
        if ($this->cost_price && $this->price > 0) {
            return (($this->price - $this->cost_price) / $this->price) * 100;
        }
        return null;
    }

    // Get profit amount
    public function getProfitAmountAttribute()
    {
        if ($this->cost_price) {
            return $this->price - $this->cost_price;
        }
        return null;
    }

    // Get formatted price
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    // Scope for active products
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for low stock products
    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'min_stock_level');
    }

    // Get stock status
    public function getStockStatusAttribute()
    {
        if ($this->quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->isLowStock()) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    // Get stock status text
    public function getStockStatusTextAttribute()
    {
        $status = $this->stock_status;
        return [
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            'in_stock' => 'In Stock'
        ][$status];
    }

    // Get stock status color
    public function getStockStatusColorAttribute()
    {
        $status = $this->stock_status;
        return [
            'out_of_stock' => 'red',
            'low_stock' => 'yellow',
            'in_stock' => 'green'
        ][$status];
    }
}