<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // İlişkiler
    public function ownedBusinesses()
    {
        return $this->hasMany(Business::class, 'owner_id');
    }

    public function managedRestaurants()
    {
        return $this->hasMany(Restaurant::class, 'restaurant_manager_id');
    }

    public function restaurantStaff()
    {
        return $this->hasMany(RestaurantStaff::class);
    }

    // Rol kontrolleri
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBusinessOwner(): bool
    {
        return $this->role === 'business_owner';
    }

    public function isRestaurantManager(): bool
    {
        return $this->role === 'restaurant_manager';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    public function isWaiter(): bool
    {
        return $this->role === 'waiter';
    }

    public function isKitchen(): bool
    {
        return $this->role === 'kitchen';
    }

    // Yardımcı metodlar
    public function getActiveBusinesses()
    {
        return $this->ownedBusinesses()->where('is_active', true)->get();
    }

    public function canAccessBusiness(Business $business): bool
    {
        return $this->isAdmin() || $business->owner_id === $this->id;
    }

    public function canAccessRestaurant(Restaurant $restaurant): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        // Yeni business_id yapısı
        if (!is_null($restaurant->business_id)) {
            if ($this->isBusinessOwner()) {
                $business = $restaurant->business;
                if ($business && $business->owner_id === $this->id) {
                    return true;
                }
            }
        }

        // Eski yapıda restaurant.user_id varsa kontrol et (geriye dönük uyumluluk)
        if (!is_null($restaurant->user_id) && $restaurant->user_id === $this->id) {
            return true;
        }

        // Aktif staff kaydı kontrolü
        return $this->restaurantStaff()
            ->where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->exists();
    }
}
