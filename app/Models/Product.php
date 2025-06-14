<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
    use HasUuidTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'uuid',

        'category_id',
        'brand_id',

        'name',
        'slug',
        'sku',

        'price',
        'sale_price',
        'stock',

        'featured',
        'status',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status'    => 'boolean',
        'featured'  => 'boolean',
    ];

    protected $with = ['primaryImage'];

    protected $appends = [
        'primary_image_url',
        'stock_status',
        'is_low_stock',
        'is_critical_stock',
        'is_overstock',
    ];

    /**
     * Get the category that owns the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the brand that owns the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * Get the images for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('is_primary', 'desc');
    }

    /**
     * Get the order items for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the cart items for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the reviews for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get only the primary image for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get all images for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('is_primary', 'desc');
    }

    /**
     * Get the primary image URL attribute.
     *
     * @return string|null
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        return $this->primaryImage?->image_url;
    }

    /**
     * Get stock status attribute
     * This method determines the stock status of the product based on its stock level and the inventory settings.
     *
     * @return string
     */
    public function getStockStatusAttribute(): string
    {
        $settings = Setting::getInventorySettings();

        if ($this->stock <= 0) {
            return 'out_of_stock';
        } elseif ($this->stock <= $settings['critical_stock_threshold']) {
            return 'critical';
        } elseif ($this->stock <= $settings['low_stock_threshold']) {
            return 'low';
        } elseif ($this->stock >= $settings['overstock_threshold']) {
            return 'overstock';
        }

        return 'in_stock';
    }

    /**
     * Check if product is low stock
     * This method checks if the product's stock is greater than zero and less than or equal to the low stock threshold.
     *
     * @return bool
     */
    public function getIsLowStockAttribute(): bool
    {
        $threshold = Setting::get('low_stock_threshold', 10);
        return $this->stock > 0 && $this->stock <= $threshold;
    }

    /**
     * Check if product is critical stock
     * This method checks if the product's stock is greater than zero and less than or equal to the critical stock threshold.
     *
     * @return bool
     */
    public function getIsCriticalStockAttribute(): bool
    {
        $threshold = Setting::get('critical_stock_threshold', 5);
        return $this->stock > 0 && $this->stock <= $threshold;
    }

    /**
     * Check if product is overstock
     * This method checks if the product's stock is greater than or equal to the overstock threshold.
     *
     * @return bool
     */
    public function getIsOverstockAttribute(): bool
    {
        $threshold = Setting::get('overstock_threshold', 1000);
        return $this->stock >= $threshold;
    }

    /**
     * Scope for low stock products
     * This scope retrieves products that have stock greater than zero and less than or equal to the low stock threshold.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLowStock($query)
    {
        $threshold = Setting::get('low_stock_threshold', 10);
        return $query->where('stock', '>', 0)->where('stock', '<=', $threshold);
    }

    /**
     * Scope for critical stock products
     * This scope retrieves products that have stock greater than zero and less than or equal to the critical stock threshold.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCriticalStock($query)
    {
        $threshold = Setting::get('critical_stock_threshold', 5);
        return $query->where('stock', '>', 0)->where('stock', '<=', $threshold);
    }

    /**
     * Scope for out of stock products
     * This scope retrieves products that have stock less than or equal to zero.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }

    /**
     * Scope for overstock products
     * This scope retrieves products that have stock greater than or equal to the overstock threshold.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverstock($query)
    {
        $threshold = Setting::get('overstock_threshold', 1000);
        return $query->where('stock', '>=', $threshold);
    }
}
