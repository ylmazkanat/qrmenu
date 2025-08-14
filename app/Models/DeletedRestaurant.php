<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedRestaurant extends Model
{
    protected $fillable = [
        'original_id',
        'business_id',
        'name',
        'slug',
        'address',
        'phone',
        'email',
        'description',
        'currency',
        'timezone',
        'logo',
        'cover',
        'is_featured',
        'is_active',
        'settings',
        'deleted_related_data',
        'deleted_at'
    ];

    protected $casts = [
        'settings' => 'array',
        'deleted_related_data' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime'
    ];
}
