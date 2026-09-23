<?php

namespace App\States\OrderStates;

class ShippedState extends OrderState
{
    public static function name(): string
    {
        return 'shipped';
    }

    public function label(): string
    {
        return 'Shipped';
    }

    public function transitions(): array
    {
        return [DeliveredState::class];
    }
}
