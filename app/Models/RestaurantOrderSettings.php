<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantOrderSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'enabled_categories',
        'ordering_enabled'
    ];

    protected $casts = [
        'enabled_categories' => 'array',
        'ordering_enabled' => 'boolean',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
} 