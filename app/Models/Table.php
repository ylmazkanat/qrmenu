<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'table_number',
        'is_active',
        'capacity',
        'location',
        'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // İlişkiler
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_number', 'table_number')
                    ->where('restaurant_id', $this->restaurant_id);
    }

    // QR URL oluşturma
    public function getQrUrlAttribute()
    {
        return route('menu.show', $this->restaurant->slug) . '#masa' . $this->table_number;
    }

    // Kategori linkli QR URL oluşturma
    public function getCategoryQrUrl($categoryId)
    {
        return $this->qr_url . '#category-' . $categoryId;
    }
}
