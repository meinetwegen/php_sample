<?php

namespace App\States\OrderStates;

class DeliveredState extends OrderState
{
    public static function name(): string
    {
        return 'delivered';
    }

    public function label(): string
    {
        return 'Delivered';
    }

    public function transitions(): array
    {
        return [];
    }
}
