<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = $request->user()->orders()
            ->with('items')
            ->withSum('items as items_count', 'quantity')
            ->latest('id')
            ->get();

        return OrderResource::collection($orders);
    }

    public function show(Request $request, int $order): OrderResource
    {
        return new OrderResource($this->own($request, $order)->load('items'));
    }

    public function store(CheckoutRequest $request): JsonResponse
    {
        $order = $this->orders->checkout($request->user(), $request->validated());

        return (new OrderResource($order))->response()->setStatusCode(201);
    }

    public function pay(Request $request, int $order): OrderResource
    {
        return new OrderResource($this->orders->pay($this->own($request, $order)));
    }

    public function cancel(Request $request, int $order): OrderResource
    {
        return new OrderResource($this->orders->cancelByCustomer($this->own($request, $order)));
    }

    /** Orders of other customers are reported as missing. */
    private function own(Request $request, int $id): Order
    {
        return $request->user()->orders()->findOrFail($id);
    }
}
