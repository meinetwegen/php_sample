<p>Hi {{ $order->user->name }},</p>

<p>Thank you for your order <strong>#{{ $order->id }}</strong>.</p>

<ul>
@foreach ($order->items as $item)
    <li>{{ $item->title }} &times; {{ $item->quantity }} &mdash; {{ number_format($item->price_cents * $item->quantity / 100, 2) }} &euro;</li>
@endforeach
</ul>

<p>
    Shipping: {{ number_format($order->shipping_cents / 100, 2) }} &euro;<br>
    <strong>Total: {{ number_format($order->total_cents / 100, 2) }} &euro;</strong>
</p>

<p>
    Delivery address: {{ $order->shipping_name }}, {{ $order->shipping_address }},
    {{ $order->shipping_zip }} {{ $order->shipping_city }}, {{ $order->shipping_country }}
</p>
