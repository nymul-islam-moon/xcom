<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'sale_price',
        'stock_quantity',
        'weight',
        'width',
        'height',
        'depth',
        'low_stock_threshold',
        'is_default'
    ];

    // ----------------------
    // Relationships
    // ----------------------

    // Variant belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Variant can have multiple images
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'variant_id');
    }

    // Variant attributes (pivot table)
    public function attributes()
    {
        return $this->belongsToMany(
            ProductAttributeValue::class,
            'product_variant_attributes',
            'product_variant_id',
            'product_attribute_value_id'
        );
    }
}
