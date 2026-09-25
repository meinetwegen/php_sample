<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function store(CategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    public function update(CategoryRequest $request, Category $category): CategoryResource
    {
        $category->update($request->validated());

        return new CategoryResource($category);
    }

    /** Magazines of the removed category stay in the catalog without a category. */
    public function destroy(Category $category): Response
    {
        $category->delete();

        return response()->noContent();
    }
}
