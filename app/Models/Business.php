<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'description',
        'logo',
        'phone',
        'address',
        'tax_number',
        'email',
        'website',
        'plan',
        'is_active',
        'plan_expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'plan_expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($business) {
            if (empty($business->slug)) {
                $business->slug = Str::slug($business->name);
                
                // Slug'ın benzersiz olduğundan emin ol
                $count = 1;
                $originalSlug = $business->slug;
                while (static::where('slug', $business->slug)->exists()) {
                    $business->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }
        });
    }

    // İlişkiler
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(BusinessSubscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(BusinessSubscription::class)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc');
    }

    // Yardımcı metodlar
    public function getTotalProductsAttribute()
    {
        return $this->restaurants()->withCount('products')->get()->sum('products_count');
    }

    public function getTotalOrdersAttribute()
    {
        return $this->restaurants()->withCount('orders')->get()->sum('orders_count');
    }

    public function getActiveRestaurantsCountAttribute()
    {
        return $this->restaurants()->where('is_active', true)->count();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    public function getCurrentPackage()
    {
        return $this->activeSubscription?->package;
    }

    public function canAccessFeature(string $featureKey): bool
    {
        $subscription = $this->activeSubscription;
        if (!$subscription) {
            return false;
        }

        $feature = $subscription->package->packageFeatures()->where('feature_key', $featureKey)->first();
        if (!$feature || !$feature->is_enabled) {
            return false;
        }

        return $feature->isUnlimited() || $feature->limit_value > 0;
    }

    public function getFeatureLimit(string $featureKey): ?int
    {
        $subscription = $this->activeSubscription;
        if (!$subscription) {
            return null;
        }

        $feature = $subscription->package->packageFeatures()->where('feature_key', $featureKey)->first();
        return $feature?->limit_value;
    }

    public function getRestaurantCountAttribute()
    {
        return $this->restaurants()->count();
    }

    public function getManagerCountAttribute()
    {
        return $this->restaurants()->select('restaurant_manager_id')->distinct()->count('restaurant_manager_id');
    }

    public function getStaffCountAttribute()
    {
        // Assuming staff are waiters, kitchen, cashiers across restaurants
        $waiters = 0;
        $kitchen = 0;
        $cashiers = 0;
        foreach ($this->restaurants as $restaurant) {
            $waiters += $restaurant->waiters()->count();
            $kitchen += $restaurant->getKitchenStaff()->count();
            $cashiers += $restaurant->getCashiers()->count();
        }
        return $waiters + $kitchen + $cashiers;
    }

    public function getProductCountAttribute()
    {
        return $this->restaurants()->withCount('products')->get()->sum('products_count');
    }

    public function getCategoryCountAttribute()
    {
        return $this->restaurants()->withCount('categories')->get()->sum('categories_count');
    }

    // For boolean features, usage is just enabled or not, but we can add if needed
}
