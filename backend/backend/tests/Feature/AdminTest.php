<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Magazine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\States\OrderStates\PendingState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): static
    {
        return $this->asUser(User::factory()->admin()->create());
    }

    private function orderFor(Magazine $magazine, int $quantity = 2, array $attributes = []): Order
    {
        $order = Order::create($attributes + [
            'user_id' => User::factory()->create()->id,
            'status' => new PendingState(),
            'payment_method' => 'card',
            'payment_status' => 'pending',
            'subtotal_cents' => 1000,
            'shipping_cents' => 500,
            'total_cents' => 1500,
            'shipping_name' => 'Ann',
            'shipping_address' => '1 Main',
            'shipping_city' => 'Astana',
            'shipping_zip' => '010000',
            'shipping_country' => 'KZ',
        ]);
        OrderItem::create(['order_id' => $order->id, 'magazine_id' => $magazine->id, 'title' => $magazine->title, 'price_cents' => 500, 'quantity' => $quantity]);

        return $order;
    }

    public function test_guests_and_customers_are_kept_out(): void
    {
        $this->getJson('/api/admin/stats')->assertUnauthorized();
        $this->asUser(User::factory()->create())->getJson('/api/admin/stats')->assertForbidden();
    }

    public function test_stats_report_counts_revenue_and_low_stock(): void
    {
        Magazine::factory()->create(['title' => 'Almost Gone', 'stock' => 2]);
        Magazine::factory()->create(['stock' => 50]);
        $this->orderFor(Magazine::factory()->create(), 1, ['payment_status' => 'paid', 'total_cents' => 2300]);

        $this->admin()->getJson('/api/admin/stats')
            ->assertOk()
            ->assertJsonPath('data.orders', 1)
            ->assertJsonPath('data.revenue_cents', 2300)
            ->assertJsonCount(14, 'data.revenue_by_day')
            ->assertJsonPath('data.revenue_by_day.13.total_cents', 2300)
            ->assertJsonFragment(['title' => 'Almost Gone']);
    }

    public function test_admin_can_create_update_and_deactivate_a_magazine(): void
    {
        $category = Category::factory()->create();

        $id = $this->admin()->postJson('/api/admin/magazines', [
            'title' => 'Brand New', 'issue_number' => '#9', 'price_cents' => 1200, 'stock' => 7, 'category_id' => $category->id,
        ])->assertCreated()->assertJsonPath('data.slug', 'brand-new-9')->assertJsonPath('data.is_active', true)->json('data.id');

        $this->admin()->putJson("/api/admin/magazines/{$id}", ['price_cents' => 1500])
            ->assertOk()
            ->assertJsonPath('data.price_cents', 1500)
            ->assertJsonPath('data.title', 'Brand New');

        $this->admin()->deleteJson("/api/admin/magazines/{$id}")->assertNoContent();
        $this->assertFalse(Magazine::find($id)->is_active);
        $this->getJson("/api/magazines/{$id}")->assertNotFound();
        $this->admin()->getJson('/api/admin/magazines')->assertJsonFragment(['id' => $id]);
    }

    public function test_magazine_input_is_validated(): void
    {
        $this->admin()->postJson('/api/admin/magazines', ['price_cents' => -1, 'format' => 'vinyl', 'category_id' => 999])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'price_cents', 'format', 'category_id']);
    }

    public function test_customer_cannot_manage_magazines(): void
    {
        $this->asUser(User::factory()->create())
            ->postJson('/api/admin/magazines', ['title' => 'X', 'price_cents' => 1])
            ->assertForbidden();
    }

    public function test_admin_manages_categories(): void
    {
        $id = $this->admin()->postJson('/api/admin/categories', ['name' => 'Travel'])
            ->assertCreated()->assertJsonPath('data.slug', 'travel')->json('data.id');

        $this->admin()->putJson("/api/admin/categories/{$id}", ['name' => 'Journeys'])
            ->assertOk()->assertJsonPath('data.name', 'Journeys');

        $magazine = Magazine::factory()->create(['category_id' => $id]);
        $this->admin()->deleteJson("/api/admin/categories/{$id}")->assertNoContent();

        $this->assertDatabaseMissing('categories', ['id' => $id]);
        $this->assertNull($magazine->fresh()->category_id);
    }

    public function test_orders_can_be_listed_and_filtered_by_status(): void
    {
        $magazine = Magazine::factory()->create();
        $this->orderFor($magazine);
        $this->orderFor($magazine, 1, ['status' => 'shipped']);

        $this->admin()->getJson('/api/admin/orders')->assertJsonCount(2, 'data');
        $this->admin()->getJson('/api/admin/orders?status=shipped')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'shipped')
            ->assertJsonStructure(['data' => [['user_email', 'items_count']]]);
    }

    public function test_order_moves_through_its_lifecycle(): void
    {
        $order = $this->orderFor(Magazine::factory()->create());

        foreach (['processing', 'shipped', 'delivered'] as $status) {
            $this->admin()->patchJson("/api/admin/orders/{$order->id}/status", ['status' => $status])
                ->assertOk()
                ->assertJsonPath('data.status', $status);
        }

        $this->admin()->patchJson("/api/admin/orders/{$order->id}/status", ['status' => 'cancelled'])->assertStatus(409);
    }

    public function test_illegal_transitions_and_unknown_statuses_are_rejected(): void
    {
        $order = $this->orderFor(Magazine::factory()->create());

        $this->admin()->patchJson("/api/admin/orders/{$order->id}/status", ['status' => 'delivered'])->assertStatus(409);
        $this->admin()->patchJson("/api/admin/orders/{$order->id}/status", ['status' => 'lost'])->assertUnprocessable();
    }

    public function test_cancelling_an_order_returns_stock(): void
    {
        $magazine = Magazine::factory()->create(['stock' => 5]);
        $order = $this->orderFor($magazine, 3);

        $this->admin()->patchJson("/api/admin/orders/{$order->id}/status", ['status' => 'cancelled'])
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertSame(8, $magazine->fresh()->stock);
    }
}
