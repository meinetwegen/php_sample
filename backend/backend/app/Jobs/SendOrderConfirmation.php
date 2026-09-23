<?php

namespace App\Jobs;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(public int $orderId) {}

    public function handle(): void
    {
        $order = Order::with(['items', 'user'])->find($this->orderId);

        if ($order) {
            Mail::to($order->user)->send(new OrderConfirmationMail($order));
        }
    }
}
