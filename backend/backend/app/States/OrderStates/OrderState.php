<?php

namespace App\States\OrderStates;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Order lifecycle: pending -> processing -> shipped -> delivered, cancellable until shipped.
 * Stored in orders.status as the state name; casts to a state object on the model.
 */
abstract class OrderState implements Castable
{
    abstract public static function name(): string;

    abstract public function label(): string;

    /** @return list<class-string<OrderState>> */
    abstract public function transitions(): array;

    /** Entering this state returns reserved stock to the shelves. */
    public function releasesStock(): bool
    {
        return false;
    }

    public function canTransitionTo(string $name): bool
    {
        foreach ($this->transitions() as $class) {
            if ($class::name() === $name) {
                return true;
            }
        }

        return false;
    }

    /** @return list<class-string<OrderState>> */
    public static function all(): array
    {
        return [PendingState::class, ProcessingState::class, ShippedState::class, DeliveredState::class, CancelledState::class];
    }

    /** @return list<string> */
    public static function names(): array
    {
        return array_map(fn (string $class) => $class::name(), self::all());
    }

    public static function make(string $name): self
    {
        foreach (self::all() as $class) {
            if ($class::name() === $name) {
                return new $class();
            }
        }

        throw new InvalidArgumentException("Unknown order state [{$name}]");
    }

    public function __toString(): string
    {
        return static::name();
    }

    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class implements CastsAttributes
        {
            public function get(Model $model, string $key, mixed $value, array $attributes): ?OrderState
            {
                return $value === null ? null : OrderState::make((string) $value);
            }

            public function set(Model $model, string $key, mixed $value, array $attributes): ?string
            {
                return $value === null ? null : (string) $value;
            }
        };
    }
}
