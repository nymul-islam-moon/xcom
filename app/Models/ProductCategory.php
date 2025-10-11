<?php

namespace App\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductCategory extends Model
{
    use HasFactory;

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

    // Boot method to handle creating/updating/deleting slugs automatically
    protected static function booted()
    {
        // When creating, generate a slug
        static::created(function ($category) {
            $slug = Str::slug($category->name);

            // Ensure uniqueness
            $count = 0;
            $originalSlug = $slug;
            while (Slug::where('slug', $slug)->exists()) {
                $count++;
                $slug = $originalSlug . '-' . $count;
            }

            $category->slugRelation()->create(['slug' => $slug]);
        });

        // When updating, update the slug if the name changes
        static::updated(function ($category) {
            if ($category->wasChanged('name')) {
                $slug = Str::slug($category->name);

                $count = 0;
                $originalSlug = $slug;
                while (Slug::where('slug', $slug)
                    ->where('sluggable_id', '!=', $category->id)
                    ->where('sluggable_type', ProductCategory::class)
                    ->exists()) {
                    $count++;
                    $slug = $originalSlug . '-' . $count;
                }

                $category->slugRelation()->updateOrCreate([], ['slug' => $slug]);
            }
        });

        // When deleting, delete the slug
        static::deleting(function ($category) {
            $category->slugRelation()->delete();
        });
    }
}
