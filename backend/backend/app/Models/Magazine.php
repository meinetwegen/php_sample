<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use App\QueryBuilders\MagazineQueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static MagazineQueryBuilder query()
 */
class Magazine extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $fillable = [
        'category_id', 'title', 'issue_number', 'description', 'price_cents',
        'stock', 'format', 'cover_url', 'published_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_cents' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'published_at' => 'date',
        ];
    }

    protected function slugSource(): string
    {
        return trim($this->title.' '.$this->issue_number);
    }

    public function newEloquentBuilder($query): MagazineQueryBuilder
    {
        return new MagazineQueryBuilder($query);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isDigital(): bool
    {
        return $this->format === 'digital';
    }
}
