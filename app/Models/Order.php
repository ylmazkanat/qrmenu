<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'table_number',
        'status',
        'total',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    /**
     * Get the restaurant that owns the order.
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the kitchen view for the order.
     */
    public function kitchenView()
    {
        return $this->hasOne(KitchenView::class);
    }

    /**
     * Calculate total from order items
     */
    public function calculateTotal()
    {
        $this->total = $this->orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });
        $this->save();
    }
}
