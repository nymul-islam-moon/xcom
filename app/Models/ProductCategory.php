<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ProductCategoryFactory> */
    use HasFactory;

    protected $table = 'product_categories';

    protected $fillable = [
        'name',
        'slug',
        'status',
        'description',
    ];

    protected $casts = [
        'status' => 'boolean',
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
                ->orWhere('slug', 'like', $like)
                ->orWhere('description', 'like', $like);
        });
    }

    public function productSubCategories()
    {
        return $this->hasMany(ProductSubCategory::class, 'product_category_id');
    }

    public function productChildCategories()
    {
        return $this->hasManyThrough(
            ProductChildCategory::class,
            ProductSubCategory::class,
            'product_category_id',
            'product_sub_category_id',
            'id',
            'id'
        );
    }
}
