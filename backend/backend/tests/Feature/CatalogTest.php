<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Magazine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_only_active_magazines(): void
    {
        Magazine::factory()->count(3)->create();
        Magazine::factory()->inactive()->create();

        $this->getJson('/api/magazines')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('meta.total', 3);
    }

    public function test_search_filters_by_title(): void
    {
        Magazine::factory()->create(['title' => 'Quantum Weekly']);
        Magazine::factory()->create(['title' => 'Garden Digest']);

        $this->getJson('/api/magazines?q=quantum')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Quantum Weekly');
    }

    public function test_filters_by_category_and_format(): void
    {
        $science = Category::factory()->create(['name' => 'Science']);
        Magazine::factory()->create(['category_id' => $science->id]);
        Magazine::factory()->digital()->create(['category_id' => $science->id]);
        Magazine::factory()->create();

        $this->getJson('/api/magazines?category=science')->assertJsonCount(2, 'data');
        $this->getJson('/api/magazines?category=science&format=digital')->assertJsonCount(1, 'data');
    }

    public function test_sorts_by_price(): void
    {
        Magazine::factory()->create(['price_cents' => 900]);
        Magazine::factory()->create(['price_cents' => 300]);

        $this->getJson('/api/magazines?sort=price_asc')->assertJsonPath('data.0.price_cents', 300);
        $this->getJson('/api/magazines?sort=price_desc')->assertJsonPath('data.0.price_cents', 900);
    }

    public function test_paginates(): void
    {
        Magazine::factory()->count(5)->create();

        $this->getJson('/api/magazines?per_page=2&page=3')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.last_page', 3);
    }

    public function test_shows_magazine_by_id_and_slug(): void
    {
        $magazine = Magazine::factory()->create(['title' => 'Future Tech', 'issue_number' => '#1']);

        $this->assertSame('future-tech-1', $magazine->slug);
        $this->getJson("/api/magazines/{$magazine->id}")->assertOk()->assertJsonPath('data.slug', 'future-tech-1');
        $this->getJson('/api/magazines/future-tech-1')->assertOk()->assertJsonPath('data.id', $magazine->id);
    }

    public function test_inactive_magazine_is_not_found(): void
    {
        $magazine = Magazine::factory()->inactive()->create();

        $this->getJson("/api/magazines/{$magazine->id}")->assertNotFound();
    }

    public function test_categories_include_active_magazine_counts(): void
    {
        $category = Category::factory()->create(['name' => 'Science']);
        Magazine::factory()->count(2)->create(['category_id' => $category->id]);
        Magazine::factory()->inactive()->create(['category_id' => $category->id]);

        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'science')
            ->assertJsonPath('data.0.magazines_count', 2);
    }
}
