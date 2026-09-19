<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->withCount(['magazines' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }
}
