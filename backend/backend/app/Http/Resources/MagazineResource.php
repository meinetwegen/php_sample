<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Magazine
 */
class MagazineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'issue_number' => $this->issue_number,
            'description' => $this->description,
            'price_cents' => $this->price_cents,
            'stock' => $this->stock,
            'format' => $this->format,
            'cover_url' => $this->cover_url,
            'published_at' => $this->published_at?->toDateString(),
            'is_active' => $this->is_active,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', fn () => $this->category
                ? ['id' => $this->category->id, 'name' => $this->category->name, 'slug' => $this->category->slug]
                : null),
        ];
    }
}
