<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitchenView extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'seen',
        'seen_at',
    ];

    protected $casts = [
        'seen' => 'boolean',
        'seen_at' => 'datetime',
    ];

    /**
     * Get the order that owns the kitchen view.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
