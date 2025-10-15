<?php

namespace App\Traits;

use App\Models\Slug;
use Illuminate\Support\Str;

trait HasSlug
{
    public static function bootHasSlug()
    {
        static::created(function ($model) {
            $source = $model->getSlugSourceAttribute();
            $slug = Str::slug($model->{$source} ?? $model->getKey());

            $slug = $model->generateUniqueSlug($slug);

            $model->{$model->getSlugRelationName()}()->create(['slug' => $slug]);
        });

        static::updated(function ($model) {
            $source = $model->getSlugSourceAttribute();

            if ($model->wasChanged($source)) {
                $slug = Str::slug($model->{$source} ?? $model->getKey());
                $slug = $model->generateUniqueSlug($slug, $model);

                $model->{$model->getSlugRelationName()}()->updateOrCreate([], ['slug' => $slug]);
            }
        });

        static::deleting(function ($model) {
            // delete via relation query (works for morphOne/morphMany)
            $model->{$model->getSlugRelationName()}()->delete();
        });
    }

    public function getSlugSourceAttribute()
    {
        return property_exists($this, 'slugSource') ? $this->slugSource : 'name';
    }

    public function getSlugRelationName()
    {
        return property_exists($this, 'slugRelationName') ? $this->slugRelationName : 'slugRelation';
    }

    /**
     * Backwards-compatible: avoid scalar type hints for old PHP versions.
     */
    public function generateUniqueSlug($base, $excludeModel = null)
    {
        $slug = $base;
        $count = 0;

        $query = Slug::where('slug', $slug);

        if ($excludeModel) {
            $query->where(function ($q) use ($excludeModel) {
                $q->where('sluggable_type', '!=', get_class($excludeModel))
                    ->orWhere('sluggable_id', '!=', $excludeModel->getKey());
            });
        }

        while ($query->exists()) {
            $count++;
            $slug = $base.'-'.$count;
            $query = Slug::where('slug', $slug);

            if ($excludeModel) {
                $query->where(function ($q) use ($excludeModel) {
                    $q->where('sluggable_type', '!=', get_class($excludeModel))
                        ->orWhere('sluggable_id', '!=', $excludeModel->getKey());
                });
            }
        }

        return $slug;
    }
}
