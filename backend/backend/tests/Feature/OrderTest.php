<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmationMail;
use App\Models\Magazine;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private array $shipping = [
        'shipping_name' => 'Ann Smith',
        'shipping_address' => '1 Main Street',
        'shipping_city' => 'Astana',
        'shipping_zip' => '010000',
        'shipping_country' => 'KZ',
        'payment_method' => 'card',
    ];

    private function checkout(User $user, Magazine $magazine, int $quantity = 2)
    {
        $this->asUser($user)->postJson('/api/cart', ['magazine_id' => $magazine->id, 'quantity' => $quantity])->assertOk();

        return $this->asUser($user)->postJson('/api/orders', $this->shipping);
    }

    public function test_checkout_creates_order_reserves_stock_and_clears_cart(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['title' => 'Future Tech', 'issue_number' => '#1', 'price_cents' => 899, 'stock' => 40]);

        $this->checkout($user, $magazine)
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.subtotal_cents', 1798)
            ->assertJsonPath('data.shipping_cents', 500)
            ->assertJsonPath('data.total_cents', 2298)
            ->assertJsonPath('data.items.0.title', 'Future Tech #1')
            ->assertJsonPath('data.items.0.quantity', 2);

        $this->assertSame(38, $magazine->fresh()->stock);
        $this->assertDatabaseCount('cart_items', 0);
        Mail::assertSent(OrderConfirmationMail::class);
    }

    public function test_empty_cart_cannot_be_checked_out(): void
    {
        $this->asUser(User::factory()->create())->postJson('/api/orders', $this->shipping)->assertUnprocessable();
    }

    public function test_shipping_details_are_validated(): void
    {
        $this->asUser(User::factory()->create())->postJson('/api/orders', ['payment_method' => 'bitcoin'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['shipping_name', 'shipping_address', 'payment_method']);
    }

    public function test_checkout_fails_when_stock_ran_out_meanwhile(): void
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['stock' => 3]);
        $this->asUser($user)->postJson('/api/cart', ['magazine_id' => $magazine->id, 'quantity' => 3])->assertOk();

        $magazine->update(['stock' => 1]); // someone else bought copies

        $this->asUser($user)->postJson('/api/orders', $this->shipping)->assertStatus(409);

        $this->assertSame(1, $magazine->fresh()->stock);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('cart_items', 1);
    }

    public function test_digital_issue_does_not_touch_stock(): void
    {
        $magazine = Magazine::factory()->digital()->create();

        $this->checkout(User::factory()->create(), $magazine)->assertCreated();

        $this->assertSame(0, $magazine->fresh()->stock);
    }

    public function test_order_can_be_paid(): void
    {
        $user = User::factory()->create();
        $orderId = $this->checkout($user, Magazine::factory()->create(['stock' => 10]))->json('data.id');

        $this->asUser($user)->postJson("/api/orders/{$orderId}/pay")
            ->assertOk()
            ->assertJsonPath('data.status', 'processing')
            ->assertJsonPath('data.payment_status', 'paid');

        $this->asUser($user)->postJson("/api/orders/{$orderId}/pay")->assertStatus(409);
    }

    public function test_pending_order_can_be_cancelled_and_stock_returns(): void
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['stock' => 10]);
        $orderId = $this->checkout($user, $magazine, 4)->json('data.id');
        $this->assertSame(6, $magazine->fresh()->stock);

        $this->asUser($user)->postJson("/api/orders/{$orderId}/cancel")
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertSame(10, $magazine->fresh()->stock);
    }

    public function test_paid_order_cannot_be_cancelled_by_the_customer(): void
    {
        $user = User::factory()->create();
        $orderId = $this->checkout($user, Magazine::factory()->create(['stock' => 10]))->json('data.id');
        $this->asUser($user)->postJson("/api/orders/{$orderId}/pay")->assertOk();

        $this->asUser($user)->postJson("/api/orders/{$orderId}/cancel")->assertStatus(409);
    }

    public function test_customer_sees_only_own_orders(): void
    {
        $ann = User::factory()->create();
        $bob = User::factory()->create();
        $orderId = $this->checkout($ann, Magazine::factory()->create(['stock' => 10]))->json('data.id');

        $this->asUser($ann)->getJson('/api/orders')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.items_count', 2);

        $this->asUser($bob)->getJson('/api/orders')->assertJsonCount(0, 'data');
        $this->asUser($bob)->getJson("/api/orders/{$orderId}")->assertNotFound();
        $this->asUser($ann)->getJson("/api/orders/{$orderId}")->assertOk()->assertJsonPath('data.id', $orderId);

        $this->assertSame(1, Order::count());
    }
}
