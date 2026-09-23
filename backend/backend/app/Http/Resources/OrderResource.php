<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Order
 */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => (string) $this->status,
            'status_label' => $this->status->label(),
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'subtotal_cents' => $this->subtotal_cents,
            'shipping_cents' => $this->shipping_cents,
            'total_cents' => $this->total_cents,
            'shipping_name' => $this->shipping_name,
            'shipping_address' => $this->shipping_address,
            'shipping_city' => $this->shipping_city,
            'shipping_zip' => $this->shipping_zip,
            'shipping_country' => $this->shipping_country,
            'created_at' => $this->created_at?->toIso8601String(),
            'items_count' => $this->when(isset($this->items_count), fn () => (int) $this->items_count),
            'user_email' => $this->whenLoaded('user', fn () => $this->user->email),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
