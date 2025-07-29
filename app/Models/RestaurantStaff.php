<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantStaff extends Model
{
    use HasFactory;

    protected $table = 'restaurant_staff';

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'role',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // İlişkiler
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Yardımcı metodlar
    public function isWaiter(): bool
    {
        return $this->role === 'waiter';
    }

    public function isKitchen(): bool
    {
        return $this->role === 'kitchen';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    public function isRestaurantManager(): bool
    {
        return $this->role === 'restaurant_manager';
    }
}
