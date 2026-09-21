<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Wraps the array returned by {@see \App\Services\CartService::summary()}.
 */
class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'items' => CartItemResource::collection($this->resource['items']),
            'count' => $this->resource['count'],
            'subtotal_cents' => $this->resource['subtotal_cents'],
            'shipping_cents' => $this->resource['shipping_cents'],
            'total_cents' => $this->resource['total_cents'],
        ];
    }
}
