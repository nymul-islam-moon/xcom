<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AttributeValue extends Model
{
    /** @use HasFactory<\Database\Factories\AttributeValueFactory> */
    use HasFactory;

    // FIX: property name should be $table
    protected $table = 'attribute_values';

    protected $fillable = ['attribute_id', 'value', 'slug'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    /**
     * Accessor so Blade can use $value->name while DB stores "value".
     */
    public function getNameAttribute(): string
    {
        return (string) ($this->attributes['value'] ?? '');
    }
}
