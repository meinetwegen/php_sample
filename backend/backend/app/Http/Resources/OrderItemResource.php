<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\OrderItem
 */
class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'magazine_id' => $this->magazine_id,
            'title' => $this->title,
            'price_cents' => $this->price_cents,
            'quantity' => $this->quantity,
        ];
    }
}
