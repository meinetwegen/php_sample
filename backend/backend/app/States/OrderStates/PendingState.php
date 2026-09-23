<?php

namespace App\States\OrderStates;

class PendingState extends OrderState
{
    public static function name(): string
    {
        return 'pending';
    }

    public function label(): string
    {
        return 'Pending';
    }

    public function transitions(): array
    {
        return [ProcessingState::class, CancelledState::class];
    }
}
