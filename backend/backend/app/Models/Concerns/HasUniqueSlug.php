<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUniqueSlug
{
    /** Text the slug is generated from. */
    abstract protected function slugSource(): string;

    protected static function bootHasUniqueSlug(): void
    {
        static::creating(function (Model $model): void {
            $base = Str::slug($model->slugSource()) ?: 'item';
            $slug = $base;
            $suffix = 2;

            while ($model->newQuery()->where('slug', $slug)->exists()) {
                $slug = $base.'-'.$suffix++;
            }

            $model->slug = $slug;
        });
    }
}
