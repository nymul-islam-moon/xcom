<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductChildCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ProductChildCategoryFactory> */
    use HasFactory;

    protected $table = 'product_child_categories';


    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'description',
        'product_sub_category_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

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
                ->orWhere('slug', 'like', $like)
                ->orWhere('description', 'like', $like);
        });
    }

    public function productSubCategory()
    {
        return $this->belongsTo(ProductSubCategory::class, 'product_sub_category_id');
    }
}
