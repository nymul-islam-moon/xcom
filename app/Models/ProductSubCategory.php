<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ProductSubCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ProductSubCategoryFactory> */
    use HasFactory;

    protected $table = 'product_sub_categories';

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'description',
        'product_category_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function productCategory(): BelongsTo
    {
        // If your FK column is product_category_id (as your logs showed)
        return $this->belongsTo(ProductCategory::class, 'product_category_id');

        // If your FK is actually category_id, use:
        // return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function productChildCategories()
    {
        return $this->hasMany(ProductChildCategory::class, 'product_sub_category_id');
    }
}
