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
        'original_status',
        'status_changed_at',
        'status_changed_by',
        'status_change_reason',
        'total',
        'paid_amount',
        'payment_status',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'status_changed_at' => 'datetime',
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

    /**
     * Status değişikliğini kaydet
     */
    public function updateStatus($newStatus, $reason = null, $changedBy = null)
    {
        // İlk kez original_status kaydediliyorsa
        if (!$this->original_status) {
            $this->original_status = $this->status;
        }
        
        $this->status = $newStatus;
        $this->status_changed_at = now();
        $this->status_changed_by = $changedBy ?? auth()->user()?->name ?? 'Sistem';
        $this->status_change_reason = $reason;
        
        $this->save();
    }

    /**
     * İptal durumunda mı kontrol et
     */
    public function isCancelled()
    {
        return in_array($this->status, ['kitchen_cancelled', 'cancelled', 'musteri_iptal']) ||
               in_array($this->original_status, ['kitchen_cancelled', 'cancelled', 'musteri_iptal']);
    }

    /**
     * Zafiyat durumunda mı kontrol et
     */
    public function isZafiyat()
    {
        return $this->status === 'zafiyat' || $this->original_status === 'zafiyat';
    }

    /**
     * Fiş için kullanılacak durum bilgisi
     */
    public function getDisplayStatus()
    {
        if ($this->isCancelled()) {
            return $this->original_status ?? $this->status;
        }
        
        if ($this->isZafiyat()) {
            return $this->original_status ?? $this->status;
        }
        
        return $this->status;
    }
}
