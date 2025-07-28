<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'restaurant_manager_id',
        'name',
        'slug',
        'description',
        'logo',
        'header_image',
        'primary_color',
        'secondary_color',
        'phone',
        'address',
        'table_count',
        'working_hours',
        'is_active',
        'custom_domain',
        'subdomain',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'working_hours' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($restaurant) {
            if (empty($restaurant->slug)) {
                $restaurant->slug = Str::slug($restaurant->name);
                
                // Slug'ın benzersiz olduğundan emin ol
                $count = 1;
                $originalSlug = $restaurant->slug;
                while (static::where('slug', $restaurant->slug)->exists()) {
                    $restaurant->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }
        });
    }

    // İlişkiler
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'restaurant_manager_id');
    }

    public function staff()
    {
        return $this->hasMany(RestaurantStaff::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function waiters()
    {
        return $this->hasMany(Waiter::class);
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

    public function orderSettings()
    {
        return $this->hasOne(RestaurantOrderSettings::class);
    }

    public function kitchenViews()
    {
        return $this->hasManyThrough(KitchenView::class, Order::class);
    }

    public function domainMappings()
    {
        return $this->hasMany(DomainMapping::class);
    }

    // Accessor'lar
    public function getMenuUrlAttribute()
    {
        if ($this->custom_domain) {
            return 'https://' . $this->custom_domain;
        }
        
        return url('/menu/' . $this->slug);
    }

    // Yardımcı metodlar
    public function getWaiters()
    {
        return $this->staff()->where('role', 'waiter')->where('is_active', true)->with('user')->get();
    }

    public function getKitchenStaff()
    {
        return $this->staff()->where('role', 'kitchen')->where('is_active', true)->with('user')->get();
    }

    public function getCashiers()
    {
        return $this->staff()->where('role', 'cashier')->where('is_active', true)->with('user')->get();
    }

    public function getTodayOrdersCount()
    {
        return $this->orders()->whereDate('created_at', today())->count();
    }

    public function getTodayRevenue()
    {
        return $this->orders()
            ->whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->sum('total');
    }
}
