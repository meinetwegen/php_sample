<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use App\States\OrderStates\OrderState;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $status = $request->query('status');

        $orders = Order::query()
            ->with('user')
            ->withSum('items as items_count', 'quantity')
            ->when(in_array($status, OrderState::names(), true), fn ($q) => $q->where('status', $status))
            ->latest('id')
            ->paginate(min(100, max(1, (int) $request->query('per_page', 20))));

        return OrderResource::collection($orders);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): OrderResource
    {
        return new OrderResource($this->orders->transition($order, $request->validated('status')));
    }
}
