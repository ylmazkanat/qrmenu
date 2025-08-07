<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Package extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle',
        'is_active',
        'is_popular',
        'sort_order',
        'features'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'price' => 'decimal:2'
    ];

    public function packageFeatures(): HasMany
    {
        return $this->hasMany(PackageFeature::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(BusinessSubscription::class);
    }

    public function getActiveSubscriptionsCountAttribute()
    {
        return $this->subscriptions()->where('status', 'active')->count();
    }

    public function getEnabledFeaturesCountAttribute()
    {
        return $this->packageFeatures()->where('is_enabled', true)->count();
    }
}
