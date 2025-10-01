<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'owner_id',
        'owner_type',
        'reference_type',
        'reference_id',
        'category',
        'sub_type',
        'direction',
        'amount',
        'currency',
        'provider',
        'transaction_id',
        'payment_method',
        'status',
        'happened_at',
        'reconciled_at',
        'meta',
        'reference_code',
        'uuid',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'amount'        => 'decimal:2',
        'happened_at'   => 'datetime',
        'reconciled_at' => 'datetime',
        'meta'          => 'array',
    ];

    /**
     * Polymorphic reference to any model (orders, subscriptions, etc.).
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Optional polymorphic owner (Shop, User, etc.).
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for filtering by category.
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for filtering by sub_type.
     */
    public function scopeSubType($query, $subType)
    {
        return $query->where('sub_type', $subType);
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering credits only.
     */
    public function scopeCredits($query)
    {
        return $query->where('direction', 'credit');
    }

    /**
     * Scope for filtering debits only.
     */
    public function scopeDebits($query)
    {
        return $query->where('direction', 'debit');
    }

    /**
     * Accessor for formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }
}
