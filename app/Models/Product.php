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
        'restock_date',
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
        'main_image',
    ];

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

    // Optional: brand
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
