<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    /** @use HasFactory<\Database\Factories\AttributeValueFactory> */
    use HasFactory;

    // FIX: property name should be $table
    protected $table = 'product_attribute_values';

    protected $fillable = ['product_attribute_id', 'value', 'slug'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    /**
     * Accessor so Blade can use $value->name while DB stores "value".
     */
    public function getNameAttribute(): string
    {
        return (string) ($this->attributes['value'] ?? '');
    }
}
