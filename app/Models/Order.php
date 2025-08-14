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
        'session_id',
        'customer_name',
        'created_by_user_id',
        'status',
        'total',
        'paid_amount',
        'payment_status',
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
     * Get the payments for the order.
     */
    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    /**
     * Get the remaining amount to be paid.
     */
    public function getRemainingAmountAttribute()
    {
        return $this->total - $this->paid_amount;
    }

    /**
     * Get the user who created this order.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
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
