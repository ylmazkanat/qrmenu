<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'stock',
        'is_available',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    /**
     * Get the restaurant that owns the product.
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if product is in stock
     */
    public function inStock(): bool
    {
        return ($this->stock > 0 || $this->stock == -1) && $this->is_available;
    }

    /**
     * Check if product has unlimited stock
     */
    public function hasUnlimitedStock(): bool
    {
        return $this->stock == -1;
    }

    /**
     * Decrease stock after order
     */
    public function decreaseStock(int $quantity): bool
    {
        if ($this->hasUnlimitedStock()) {
            return true; // Sınırsız stok, hiçbir şey yapma
        }

        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
            return true;
        }

        return false; // Yetersiz stok
    }

    /**
     * Get stock display text
     */
    public function getStockDisplayAttribute(): string
    {
        if ($this->hasUnlimitedStock()) {
            return 'Sınırsız';
        }
        return (string) $this->stock;
    }
}
