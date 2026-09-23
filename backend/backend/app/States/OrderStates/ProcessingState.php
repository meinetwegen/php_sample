<?php

namespace App\States\OrderStates;

class ProcessingState extends OrderState
{
    public static function name(): string
    {
        return 'processing';
    }

    public function label(): string
    {
        return 'Processing';
    }

    public function transitions(): array
    {
        return [ShippedState::class, CancelledState::class];
    }
}
