<?php

namespace App\Services;

use App\Exceptions\EmptyCartException;
use App\Exceptions\InvalidTransitionException;
use App\Exceptions\OutOfStockException;
use App\Jobs\SendOrderConfirmation;
use App\Models\Magazine;
use App\Models\Order;
use App\Models\User;
use App\States\OrderStates\CancelledState;
use App\States\OrderStates\OrderState;
use App\States\OrderStates\PendingState;
use App\States\OrderStates\ProcessingState;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(private readonly CartService $cart) {}

    /**
     * Turns the user's cart into an order. Stock is reserved with an atomic
     * conditional UPDATE, so the last copy can never be sold twice.
     *
     * @param  array<string, string>  $details  shipping_* fields and payment_method
     */
    public function checkout(User $user, array $details): Order
    {
        $order = DB::transaction(function () use ($user, $details) {
            $items = $this->cart->items($user);

            if ($items->isEmpty()) {
                throw new EmptyCartException();
            }

            $subtotal = 0;
            foreach ($items as $item) {
                $magazine = $item->magazine;

                if (! $magazine->isDigital()) {
                    $reserved = Magazine::whereKey($magazine->id)
                        ->where('stock', '>=', $item->quantity)
                        ->decrement('stock', $item->quantity);

                    if ($reserved === 0) {
                        throw new OutOfStockException($magazine->title, $magazine->fresh()->stock);
                    }
                }

                $subtotal += $magazine->price_cents * $item->quantity;
            }

            $shipping = $this->cart->shipping($items, $subtotal);

            $order = Order::create($details + [
                'user_id' => $user->id,
                'status' => new PendingState(),
                'payment_status' => 'pending',
                'subtotal_cents' => $subtotal,
                'shipping_cents' => $shipping,
                'total_cents' => $subtotal + $shipping,
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'magazine_id' => $item->magazine_id,
                    'title' => trim($item->magazine->title.' '.$item->magazine->issue_number),
                    'price_cents' => $item->magazine->price_cents,
                    'quantity' => $item->quantity,
                ]);
            }

            $this->cart->clear($user);

            return $order;
        });

        SendOrderConfirmation::dispatch($order->id);

        return $order->load('items');
    }

    /** Mock payment; replace with a Stripe/PayPal flow confirmed by a webhook. */
    public function pay(Order $order): Order
    {
        if (! $order->status instanceof PendingState || $order->payment_status !== 'pending') {
            throw new InvalidTransitionException('This order cannot be paid');
        }

        $order->payment_status = 'paid';
        $order->status = new ProcessingState();
        $order->save();

        return $order->load('items');
    }

    /** Customers may only cancel orders that are still pending. */
    public function cancelByCustomer(Order $order): Order
    {
        if (! $order->status instanceof PendingState) {
            throw new InvalidTransitionException('Only pending orders can be cancelled');
        }

        return $this->transition($order, CancelledState::name());
    }

    public function transition(Order $order, string $to): Order
    {
        $current = $order->status;

        if (! $current->canTransitionTo($to)) {
            throw new InvalidTransitionException("Cannot move an order from {$current} to {$to}");
        }

        $next = OrderState::make($to);

        DB::transaction(function () use ($order, $next) {
            if ($next->releasesStock()) {
                $this->restock($order);
            }

            $order->status = $next;
            $order->save();
        });

        return $order->load('items');
    }

    private function restock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->magazine_id === null) {
                continue;
            }

            Magazine::whereKey($item->magazine_id)
                ->where('format', '!=', 'digital')
                ->increment('stock', $item->quantity);
        }
    }
}
