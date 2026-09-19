<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Magazine;
use Illuminate\Database\Seeder;

class MagazineSeeder extends Seeder
{
    public function run(): void
    {
        if (Magazine::query()->exists()) {
            return;
        }

        $categories = Category::pluck('id', 'slug');

        $samples = [
            ['Future Tech', '#1', 'technology', 'AI, robotics and gadgets of tomorrow.', 899, 40, 'print'],
            ['Future Tech', '#2', 'technology', 'Quantum computing special.', 899, 25, 'both'],
            ['Science Weekly', '#118', 'science', 'Deep space and deep sea.', 650, 0, 'digital'],
            ['Modern Living', '#7', 'lifestyle', 'Minimalism, food and travel.', 1099, 15, 'print'],
            ['Runway', '#33', 'fashion', 'Autumn collections report.', 1299, 10, 'print'],
            ['Market Pulse', '#52', 'business', 'Startups and investing.', 750, 30, 'both'],
        ];

        foreach ($samples as $i => [$title, $issue, $category, $description, $price, $stock, $format]) {
            Magazine::create([
                'category_id' => $categories[$category] ?? null,
                'title' => $title,
                'issue_number' => $issue,
                'description' => $description,
                'price_cents' => $price,
                'stock' => $stock,
                'format' => $format,
                'published_at' => now()->subDays(($i + 1) * 9)->toDateString(),
            ]);
        }
    }
}
