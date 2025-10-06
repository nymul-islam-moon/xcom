<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Shop extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\ShopFactory> */
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     * This is used to specify the database table that this model corresponds to.
     * If not specified, Laravel will assume the table name is the plural form of the model name.
     */
    protected $table = 'shops';

    /**
     * The authentication guard for the admin model.
     * This is used to specify which guard should be used for authentication.
     */
    protected $guard = 'shop';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'slug',
        'shop_keeper_name',
        'shop_keeper_phone',
        'shop_keeper_email',
        'shop_keeper_photo',
        'shop_keeper_tin',
        'shop_keeper_nid',
        'dbid',
        'bank_name',
        'is_active',
        'status',
        'bank_account_number',
        'bank_branch',
        'shop_logo',
        'description',
        'business_address',
        'password',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function accounts()
    {
        return $this->morphMany(\App\Models\Account::class, 'owner');
    }

    public function payments()
    {
        return $this->hasMany(ShopPayment::class);
    }

    /**
     * Return the best applicable start_date or end_date for the collection.
     *
     * @param  string  $which  'end_date' or 'start_date'
     * @return string 'Y-m-d' formatted date or 'Not Subscribed'
     */
    public function subscriptionDate(string $which = 'end_date'): string
    {
        $which = $which === 'start_date' ? 'start_date' : 'end_date'; // sanitize

        if ($this->payments->isEmpty()) {
            return 'Not Subscribed';
        }

        $today = \Carbon\Carbon::now()->startOfDay();

        // Map over payments relation
        $payments = $this->payments->map(function ($p) {
            $p->start_obj = $p->start_date ? \Carbon\Carbon::parse($p->start_date)->startOfDay() : null;
            $p->end_obj = $p->end_date ? \Carbon\Carbon::parse($p->end_date)->endOfDay() : null;

            return $p;
        });

        // 1) Active subscriptions
        $active = $payments
            ->filter(fn ($p) => $p->start_obj && $p->end_obj && $today->between($p->start_obj, $p->end_obj))
            ->sortByDesc(fn ($p) => $p->end_obj->timestamp)
            ->first();

        if ($active) {
            return $which === 'start_date'
                ? ($active->start_obj ? $active->start_obj->format('Y-m-d') : 'Not Subscribed')
                : ($active->end_obj ? $active->end_obj->format('Y-m-d') : 'Not Subscribed');
        }

        // 2) Future subscriptions
        $future = $payments
            ->filter(fn ($p) => $p->start_obj && $p->start_obj->gt($today))
            ->sortBy(fn ($p) => $p->start_obj->timestamp)
            ->first();

        if ($future) {
            return $which === 'start_date'
                ? ($future->start_obj ? $future->start_obj->format('Y-m-d') : 'Not Subscribed')
                : ($future->end_obj ? $future->end_obj->format('Y-m-d') : 'Not Subscribed');
        }

        // 3) Past subscriptions
        $past = $payments
            ->filter(fn ($p) => $p->end_obj && $p->end_obj->lt($today))
            ->sortByDesc(fn ($p) => $p->end_obj->timestamp)
            ->first();

        if ($past) {
            return $which === 'start_date'
                ? ($past->start_obj ? $past->start_obj->format('Y-m-d') : 'Not Subscribed')
                : ($past->end_obj ? $past->end_obj->format('Y-m-d') : 'Not Subscribed');
        }

        return 'Not Subscribed';
    }

    /**
     * It will use for validation check
     * 1. Shop is active or not
     * 2.
     */
    public function validateShopUser()
    {
        if (is_null($this->email_verified_at)) {
            return 'Your email is not verified. Please verify your email to continue.';
        }

        switch ($this->status) {
            case 'suspended':
                return 'Your shop account is suspended. Please contact support.';

            case 'inactive':
                return 'Your shop account is inactive. Please activate it to continue.';

            case 'pending':
                return 'Your shop account is pending approval. Please wait for admin approval.';

            case 'expired':
                return 'Your shop subscription has expired. Please renew to continue using your account.';

            case 'active':
                return true;

            default:
                return 'Unknown shop status. Please contact support.';
        }
    }

    /** Scope: search by name/email/phone */
    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }

        $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';

        return $query->where(function ($w) use ($like) {
            $w->where('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('phone', 'like', $like);
        });
    }
}
