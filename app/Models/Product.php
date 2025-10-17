<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    // Fillable columns
    protected $fillable = [
        'shop_id',
        'name',
        'sku',
        'slug',
        'short_description',
        'description',
        'product_type',
        'variant_type',
        'category_id',
        'subcategory_id',
        'child_category_id',
        'brand_id',
        'status',
        'is_featured',
        'tax_included',
        'tax_percentage',
        'allow_backorders',
        'mpn',
        'gtin8',
        'gtin13',
        'gtin14',
        'return_policy',
        'return_days',
        'publish_date',
        'is_published',
        'download_url',
        'license_key',
        'subscription_interval',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // ----------------------
    // Relationships
    // ----------------------

    // A product has many variants
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Product main gallery images
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function mainImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_default', 1);
    }

    // Optional: category/subcategory/child_category relations
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(ProductSubCategory::class, 'subcategory_id');
    }

    public function childCategory()
    {
        return $this->belongsTo(ProductChildCategory::class, 'child_category_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    // Optional: brand
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
