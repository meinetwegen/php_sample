<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartItemRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function show(Request $request): CartResource
    {
        return $this->respond($request);
    }

    public function store(CartItemRequest $request): CartResource
    {
        $this->cart->add($request->user(), $request->validated('magazine_id'), $request->validated('quantity') ?? 1);

        return $this->respond($request);
    }

    public function update(Request $request, int $magazine): CartResource
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:'.config('shop.max_cart_quantity')],
        ]);

        $this->cart->setQuantity($request->user(), $magazine, $data['quantity']);

        return $this->respond($request);
    }

    public function destroy(Request $request, int $magazine): CartResource
    {
        $this->cart->setQuantity($request->user(), $magazine, 0);

        return $this->respond($request);
    }

    public function clear(Request $request): CartResource
    {
        $this->cart->clear($request->user());

        return $this->respond($request);
    }

    private function respond(Request $request): CartResource
    {
        return new CartResource($this->cart->summary($request->user()));
    }
}
