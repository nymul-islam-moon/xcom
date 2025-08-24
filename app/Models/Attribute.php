<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    /** @use HasFactory<\Database\Factories\AttributeFactory> */
    use HasFactory;

    protected $table = 'attributes';

    protected $fillable = [
        'name',
        'slug',
    ];

    /** Scope: search by name/slug/description */
    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }

        // escape wildcards to avoid weird matches
        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';

        return $query->where(function ($w) use ($like) {
            $w->where('name', 'like', $like)
                ->orWhere('slug', 'like', $like);
        });
    }


    // /**
    //  * Get the attribute values for the attribute.
    //  */
    // public function values()
    // {
    //     return $this->hasMany(AttributeValue::class);
    // }
}
