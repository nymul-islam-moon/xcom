<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductCategory extends Model
{
    use HasFactory, HasSlug;

    protected $table = 'product_categories';

    protected $fillable = [
        'name',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        $category = $this->whereHas('slugRelation', function ($query) use ($value) {
            $query->where('slug', $value);
        })->first();

        if (!$category) {
            throw (new ModelNotFoundException)->setModel(static::class, $value);
        }

        return $category;
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

    // Morph relation to Slug
    public function slugRelation()
    {
        return $this->morphOne(Slug::class, 'sluggable');
    }
}
