<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Magazine;
use App\Models\Order;
use App\Models\User;
use App\States\OrderStates\CancelledState;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    private const CHART_DAYS = 14;

    public function __invoke(): JsonResponse
    {
        $revenue = Order::query()->where('payment_status', 'paid')->where('status', '!=', CancelledState::name());

        return response()->json(['data' => [
            'users' => User::where('role', 'customer')->count(),
            'magazines' => Magazine::query()->active()->count(),
            'orders' => Order::count(),
            'revenue_cents' => (int) (clone $revenue)->sum('total_cents'),
            'revenue_by_day' => $this->revenueByDay(clone $revenue),
            'low_stock' => Magazine::query()->active()
                ->where('format', '!=', 'digital')
                ->where('stock', '<=', 5)
                ->orderBy('stock')
                ->get(['id', 'title', 'issue_number', 'stock']),
        ]]);
    }

    /** Paid revenue per day for the last two weeks, zero-filled for the chart. */
    private function revenueByDay($paidOrders): array
    {
        $from = now()->subDays(self::CHART_DAYS - 1)->startOfDay();

        $totals = $paidOrders->where('created_at', '>=', $from)->get(['total_cents', 'created_at'])
            ->groupBy(fn (Order $order) => $order->created_at->toDateString())
            ->map(fn ($orders) => (int) $orders->sum('total_cents'));

        return collect(range(0, self::CHART_DAYS - 1))
            ->map(function (int $offset) use ($from, $totals) {
                $date = $from->copy()->addDays($offset)->toDateString();

                return ['date' => $date, 'total_cents' => $totals->get($date, 0)];
            })
            ->all();
    }
}
