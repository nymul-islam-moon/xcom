<?php

namespace App\Traits;

use Illuminate\Support\Str;
use App\Models\Slug;

trait HasSlug
{
    /**
     * Boot the trait - Laravel will automatically call boot{TraitName}
     */
    public static function bootHasSlug()
    {
        // After creating - generate slug (id exists)
        static::created(function ($model) {
            $source = $model->getSlugSourceAttribute();
            $slug = Str::slug($model->{$source} ?? $model->getKey());

            $slug = $model->generateUniqueSlug($slug);

            // create morphOne/polymorphic relation (expects relation method exists on model)
            $model->{$model->getSlugRelationName()}()->create(['slug' => $slug]);
        });

        // After updating - update slug if source changed
        static::updated(function ($model) {
            $source = $model->getSlugSourceAttribute();

            if ($model->wasChanged($source)) {
                $slug = Str::slug($model->{$source} ?? $model->getKey());
                $slug = $model->generateUniqueSlug($slug, $model);

                // update or create relation (safe if no slug exists yet)
                $model->{$model->getSlugRelationName()}()->updateOrCreate([], ['slug' => $slug]);
            }
        });

        // On delete - remove related slug record
        static::deleting(function ($model) {
            // If relation exists, delete it.
            $relation = $model->{$model->getSlugRelationName()}();

            if ($relation) {
                $relation->delete();
            }
        });
    }

    /**
     * Returns model property name to use as slug source (default 'name').
     * You may override on model: protected $slugSource = 'title';
     */
    public function getSlugSourceAttribute()
    {
        return property_exists($this, 'slugSource') ? $this->slugSource : 'name';
    }

    /**
     * Returns the relation method name used for the slug (default 'slugRelation').
     * You may override on model: protected $slugRelationName = 'mySlugRelation';
     */
    public function getSlugRelationName()
    {
        return property_exists($this, 'slugRelationName') ? $this->slugRelationName : 'slugRelation';
    }

    /**
     * Generate a unique slug. If $excludeModel is provided, exclude that model's own slug when checking uniqueness.
     *
     * @param  string  $base
     * @param  \Illuminate\Database\Eloquent\Model|null  $excludeModel
     * @return string
     */
    public function generateUniqueSlug(string $base, $excludeModel = null)
    {
        $slug = $base;
        $count = 0;
        $query = Slug::where('slug', $slug);

        if ($excludeModel) {
            // exclude the existing slug of this model (for update)
            $query->where(function ($q) use ($excludeModel) {
                $q->where('sluggable_type', '!=', get_class($excludeModel))
                  ->orWhere('sluggable_id', '!=', $excludeModel->getKey());
            });
        }

        while ($query->exists()) {
            $count++;
            $slug = $base . '-' . $count;
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
