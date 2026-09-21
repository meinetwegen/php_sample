<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\CartItem
 */
class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'magazine_id' => $this->magazine_id,
            'title' => $this->magazine->title,
            'slug' => $this->magazine->slug,
            'issue_number' => $this->magazine->issue_number,
            'cover_url' => $this->magazine->cover_url,
            'price_cents' => $this->magazine->price_cents,
            'stock' => $this->magazine->stock,
            'format' => $this->magazine->format,
            'quantity' => $this->quantity,
        ];
    }
}
