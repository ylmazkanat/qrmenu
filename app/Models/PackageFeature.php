<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageFeature extends Model
{
    protected $fillable = [
        'package_id',
        'feature_key',
        'feature_name',
        'description',
        'limit_value',
        'is_enabled',
        'sort_order'
    ];

    protected $casts = [
        'limit_value' => 'integer',
        'is_enabled' => 'boolean'
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function isUnlimited(): bool
    {
        return is_null($this->limit_value);
    }
}
