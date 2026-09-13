<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $fillable = ['name'];

    protected function slugSource(): string
    {
        return (string) $this->name;
    }

    public function magazines(): HasMany
    {
        return $this->hasMany(Magazine::class);
    }
}
