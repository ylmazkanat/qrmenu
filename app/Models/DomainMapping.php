<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'domain_type',
        'domain',
        'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    /**
     * Get the restaurant that owns the domain mapping.
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
