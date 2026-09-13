<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<\App\Models\Magazine>
 */
class MagazineQueryBuilder extends Builder
{
    public function active(): static
    {
        return $this->where('is_active', true);
    }

    public function search(?string $term): static
    {
        if ($term === null || trim($term) === '') {
            return $this;
        }

        return $this->where(fn (Builder $q) => $q
            ->where('title', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%"));
    }

    public function inCategory(?string $slug): static
    {
        return $slug ? $this->whereHas('category', fn (Builder $q) => $q->where('slug', $slug)) : $this;
    }

    public function ofFormat(?string $format): static
    {
        return in_array($format, ['print', 'digital', 'both'], true) ? $this->where('format', $format) : $this;
    }

    public function sortBy(?string $sort): static
    {
        return match ($sort) {
            'price_asc' => $this->orderBy('price_cents'),
            'price_desc' => $this->orderByDesc('price_cents'),
            'title' => $this->orderBy('title')->orderBy('issue_number'),
            default => $this->orderByDesc('published_at')->orderByDesc('id'),
        };
    }

    /** @param  array{q?: string, category?: string, format?: string, sort?: string}  $filters */
    public function applyFilters(array $filters): static
    {
        return $this->search($filters['q'] ?? null)
            ->inCategory($filters['category'] ?? null)
            ->ofFormat($filters['format'] ?? null)
            ->sortBy($filters['sort'] ?? null);
    }
}
