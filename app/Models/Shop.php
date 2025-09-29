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

        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';

        return $query->where(function ($w) use ($like) {
            $w->where('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('phone', 'like', $like);
        });
    }
}
