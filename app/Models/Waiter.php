<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waiter extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'name',
    ];

    /**
     * Get the restaurant that owns the waiter.
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
