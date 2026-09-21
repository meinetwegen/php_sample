<?php

namespace App\Services;

use App\Exceptions\OutOfStockException;
use App\Models\CartItem;
use App\Models\Magazine;
use App\Models\User;
use Illuminate\Support\Collection;

class CartService
{
    /** @return Collection<int, CartItem> */
    public function items(User $user): Collection
    {
        return $user->cartItems()
            ->with('magazine')
            ->whereHas('magazine', fn ($q) => $q->where('is_active', true))
            ->get()
            ->sortBy(fn (CartItem $item) => $item->magazine->title)
            ->values();
    }

    /** @return array{items: Collection, count: int, subtotal_cents: int, shipping_cents: int, total_cents: int} */
    public function summary(User $user): array
    {
        $items = $this->items($user);
        $subtotal = (int) $items->sum(fn (CartItem $item) => $item->magazine->price_cents * $item->quantity);
        $shipping = $this->shipping($items, $subtotal);

        return [
            'items' => $items,
            'count' => (int) $items->sum('quantity'),
            'subtotal_cents' => $subtotal,
            'shipping_cents' => $shipping,
            'total_cents' => $subtotal + $shipping,
        ];
    }

    /** Digital-only carts ship free; otherwise a flat fee applies below the free-shipping threshold. */
    public function shipping(Collection $items, int $subtotal): int
    {
        $hasPhysical = $items->contains(fn (CartItem $item) => ! $item->magazine->isDigital());

        if (! $hasPhysical || $subtotal >= config('shop.free_shipping_cents')) {
            return 0;
        }

        return config('shop.shipping_flat_cents');
    }

    public function add(User $user, int $magazineId, int $quantity): void
    {
        $current = (int) $user->cartItems()->where('magazine_id', $magazineId)->value('quantity');

        $this->setQuantity($user, $magazineId, $current + $quantity);
    }

    /** Quantity 0 removes the line. */
    public function setQuantity(User $user, int $magazineId, int $quantity): void
    {
        if ($quantity === 0) {
            $user->cartItems()->where('magazine_id', $magazineId)->delete();

            return;
        }

        $magazine = Magazine::query()->active()->findOrFail($magazineId);

        if ($magazine->isDigital()) {
            $quantity = 1; // one licence per digital issue
        } elseif ($quantity > $magazine->stock) {
            throw new OutOfStockException($magazine->title, $magazine->stock);
        }

        $user->cartItems()->updateOrCreate(['magazine_id' => $magazineId], ['quantity' => $quantity]);
    }

    public function clear(User $user): void
    {
        $user->cartItems()->delete();
    }
}
