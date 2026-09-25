<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MagazineRequest;
use App\Http\Resources\MagazineResource;
use App\Models\Magazine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MagazineController extends Controller
{
    /** Same filters as the public catalog, but inactive issues are included. */
    public function index(Request $request): AnonymousResourceCollection
    {
        $magazines = Magazine::query()
            ->with('category')
            ->applyFilters($request->only(['q', 'category', 'format', 'sort']))
            ->paginate(min(100, max(1, (int) $request->query('per_page', 20))));

        return MagazineResource::collection($magazines);
    }

    public function store(MagazineRequest $request): JsonResponse
    {
        $magazine = Magazine::create($request->validated())->refresh()->load('category');

        return (new MagazineResource($magazine))->response()->setStatusCode(201);
    }

    public function update(MagazineRequest $request, Magazine $magazine): MagazineResource
    {
        $magazine->update($request->validated());

        return new MagazineResource($magazine->load('category'));
    }

    /** Soft delete: issues referenced by past orders must stay in the database. */
    public function destroy(Magazine $magazine): Response
    {
        $magazine->update(['is_active' => false]);

        return response()->noContent();
    }
}
