<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Magazine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Magazine>
 */
class MagazineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'title' => fake()->unique()->words(3, true),
            'issue_number' => '#'.fake()->numberBetween(1, 200),
            'description' => fake()->sentence(),
            'price_cents' => fake()->numberBetween(300, 2500),
            'stock' => fake()->numberBetween(5, 50),
            'format' => 'print',
            'published_at' => fake()->date(),
            'is_active' => true,
        ];
    }

    public function digital(): static
    {
        return $this->state(['format' => 'digital', 'stock' => 0]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
