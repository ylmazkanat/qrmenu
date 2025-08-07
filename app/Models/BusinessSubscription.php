<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessSubscription extends Model
{
    protected $fillable = [
        'business_id',
        'package_id',
        'status',
        'started_at',
        'expires_at',
        'cancelled_at',
        'amount_paid',
        'payment_method',
        'transaction_id',
        'usage_limits',
        'is_paid',
        'payment_date',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount_paid' => 'decimal:2',
        'usage_limits' => 'array',
        'is_paid' => 'boolean',
        'payment_date' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function isActive(): bool
    {
        if ($this->status === 'pending_cancellation' && $this->expires_at->isPast()) {
            $this->status = 'cancelled';
            $this->save();
        }
        return in_array($this->status, ['active', 'pending_cancellation']) && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
