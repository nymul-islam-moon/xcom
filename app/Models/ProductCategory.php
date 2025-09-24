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
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
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
