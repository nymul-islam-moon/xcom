<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopPayment extends Model
{
    protected $table = 'shop_payments';

    protected $fillable = [
        'shop_id',
        'payment_method',
        'transaction_id',
        'payment_date',
        'start_date',
        'duration_days',
        'end_date',
        'status',
    ];

    protected $dates = [
        'payment_date',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];

    /**
     * Relationship: ShopPayment belongs to a Shop
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Check if the payment is currently active
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' && now()->lte($this->end_date);
    }

    /**
     * Accessor: Check if payment is expired
     */
    public function getIsExpiredAttribute(): bool
    {
        return now()->gt($this->end_date);
    }

    protected static function booted()
    {
        static::saving(function ($payment) {
            if ($payment->start_date && $payment->duration_days) {
                $payment->end_date = \Carbon\Carbon::parse($payment->start_date)
                    ->addDays($payment->duration_days);
            }
        });
    }
}
