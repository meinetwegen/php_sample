<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Technology', 'Science', 'Lifestyle', 'Fashion', 'Business'] as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
