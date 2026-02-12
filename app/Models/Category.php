<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'status',
        'image',
        'parent_id',
        'slug',          // <-- ADD THIS
        'created_by',
        'updated_by'
    ];

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'status' => 'active',
    ];

    /**
     * Boot the model – auto‑generate slug when creating a category.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        // Optional: update slug when name changes
        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // ─────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────────────────

    /**
     * Get the products for the category.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the subcategories.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // ─────────────────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────────────────

    /**
     * Scope for active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for root categories (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    // ─────────────────────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────────────────────

    /**
     * Get the total products count.
     */
    public function getTotalProductsAttribute()
    {
        return $this->products()->count();
    }

    /**
     * Get the total active products count.
     */
    public function getActiveProductsCountAttribute()
    {
        return $this->products()->where('status', 'active')->count();
    }
}