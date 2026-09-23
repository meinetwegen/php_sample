<?php

namespace App\Models;

use App\States\OrderStates\OrderState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'status', 'payment_method', 'payment_status',
        'subtotal_cents', 'shipping_cents', 'total_cents',
        'shipping_name', 'shipping_address', 'shipping_city', 'shipping_zip', 'shipping_country',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderState::class,
            'subtotal_cents' => 'integer',
            'shipping_cents' => 'integer',
            'total_cents' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
