<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
