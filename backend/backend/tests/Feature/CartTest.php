<?php

namespace Tests\Feature;

use App\Models\Magazine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_requires_authentication(): void
    {
        $this->getJson('/api/cart')->assertUnauthorized();
    }

    public function test_item_can_be_added_and_quantities_add_up(): void
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['price_cents' => 899, 'stock' => 10]);

        $this->asUser($user)->postJson('/api/cart', ['magazine_id' => $magazine->id, 'quantity' => 2])->assertOk();
        $this->asUser($user)->postJson('/api/cart', ['magazine_id' => $magazine->id])
            ->assertOk()
            ->assertJsonPath('data.count', 3)
            ->assertJsonPath('data.subtotal_cents', 2697)
            ->assertJsonPath('data.items.0.quantity', 3);
    }

    public function test_cannot_exceed_stock(): void
    {
        $magazine = Magazine::factory()->create(['stock' => 3]);

        $this->asUser(User::factory()->create())
            ->postJson('/api/cart', ['magazine_id' => $magazine->id, 'quantity' => 4])
            ->assertStatus(409);
    }

    public function test_quantity_is_validated(): void
    {
        $magazine = Magazine::factory()->create();

        $this->asUser(User::factory()->create())
            ->postJson('/api/cart', ['magazine_id' => $magazine->id, 'quantity' => 99])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['quantity']);
    }

    public function test_digital_issue_is_limited_to_one_licence(): void
    {
        $magazine = Magazine::factory()->digital()->create();

        $this->asUser(User::factory()->create())
            ->postJson('/api/cart', ['magazine_id' => $magazine->id, 'quantity' => 5])
            ->assertOk()
            ->assertJsonPath('data.items.0.quantity', 1);
    }

    public function test_inactive_magazine_cannot_be_added(): void
    {
        $magazine = Magazine::factory()->inactive()->create();

        $this->asUser(User::factory()->create())
            ->postJson('/api/cart', ['magazine_id' => $magazine->id])
            ->assertNotFound();
    }

    public function test_quantity_can_be_changed_and_line_removed(): void
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['stock' => 10]);
        $this->asUser($user)->postJson('/api/cart', ['magazine_id' => $magazine->id]);

        $this->asUser($user)->putJson("/api/cart/{$magazine->id}", ['quantity' => 4])
            ->assertOk()
            ->assertJsonPath('data.count', 4);

        $this->asUser($user)->putJson("/api/cart/{$magazine->id}", ['quantity' => 0])
            ->assertOk()
            ->assertJsonPath('data.count', 0);
    }

    public function test_line_can_be_deleted_and_cart_cleared(): void
    {
        $user = User::factory()->create();
        [$a, $b] = Magazine::factory()->count(2)->create(['stock' => 10]);
        $this->asUser($user)->postJson('/api/cart', ['magazine_id' => $a->id]);
        $this->asUser($user)->postJson('/api/cart', ['magazine_id' => $b->id]);

        $this->asUser($user)->deleteJson("/api/cart/{$a->id}")->assertOk()->assertJsonPath('data.count', 1);
        $this->asUser($user)->deleteJson('/api/cart')->assertOk()->assertJsonPath('data.count', 0);
    }

    public function test_shipping_is_charged_below_the_threshold_and_free_above(): void
    {
        $user = User::factory()->create();
        $cheap = Magazine::factory()->create(['price_cents' => 899, 'stock' => 10]);
        $pricey = Magazine::factory()->create(['price_cents' => 6000, 'stock' => 10]);

        $this->asUser($user)->postJson('/api/cart', ['magazine_id' => $cheap->id, 'quantity' => 2])
            ->assertJsonPath('data.shipping_cents', 500)
            ->assertJsonPath('data.total_cents', 2298);

        $this->asUser($user)->postJson('/api/cart', ['magazine_id' => $pricey->id])
            ->assertJsonPath('data.shipping_cents', 0);
    }

    public function test_digital_only_cart_ships_free(): void
    {
        $magazine = Magazine::factory()->digital()->create(['price_cents' => 650]);

        $this->asUser(User::factory()->create())
            ->postJson('/api/cart', ['magazine_id' => $magazine->id])
            ->assertJsonPath('data.shipping_cents', 0)
            ->assertJsonPath('data.total_cents', 650);
    }
}
