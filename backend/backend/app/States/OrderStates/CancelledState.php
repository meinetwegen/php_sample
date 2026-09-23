<?php

namespace App\States\OrderStates;

class CancelledState extends OrderState
{
    public static function name(): string
    {
        return 'cancelled';
    }

    public function label(): string
    {
        return 'Cancelled';
    }

    public function transitions(): array
    {
        return [];
    }

    public function releasesStock(): bool
    {
        return true;
    }
}
