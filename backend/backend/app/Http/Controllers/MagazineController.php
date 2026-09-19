<?php

namespace App\Http\Controllers;

use App\Http\Resources\MagazineResource;
use App\Models\Magazine;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MagazineController extends Controller
{
    /** Query: q, category (slug), format, sort (newest|price_asc|price_desc|title), page, per_page */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(50, max(1, (int) $request->query('per_page', 12)));

        $magazines = Magazine::query()
            ->active()
            ->with('category')
            ->applyFilters($request->only(['q', 'category', 'format', 'sort']))
            ->paginate($perPage);

        return MagazineResource::collection($magazines);
    }

    /** Accepts a numeric id or a slug. */
    public function show(string $magazine): MagazineResource
    {
        $found = Magazine::query()
            ->active()
            ->with('category')
            ->where(ctype_digit($magazine) ? 'id' : 'slug', $magazine)
            ->firstOrFail();

        return new MagazineResource($found);
    }
}
