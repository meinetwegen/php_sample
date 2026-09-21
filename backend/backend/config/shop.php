<?php

return [
    // Flat shipping fee, waived for physical orders at or above the threshold (cents).
    'shipping_flat_cents' => (int) env('SHIPPING_FLAT_CENTS', 500),
    'free_shipping_cents' => (int) env('FREE_SHIPPING_CENTS', 5000),
    'max_cart_quantity' => 20,
];
