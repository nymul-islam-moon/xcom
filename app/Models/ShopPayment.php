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
        'payment_date',
        'start_date',
        'duration_days',
        'end_date',
    ];

    protected $dates = [
        'payment_date',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Relationship: ShopPayment belongs to a Shop
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Accessor: Check if payment is expired
     */
    public function getIsExpiredAttribute(): bool
    {
        return now()->gt($this->end_date);
    }
}
