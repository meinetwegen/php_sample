<?php

namespace App\Http\Controllers\Swagger;

use OpenApi\Attributes as OA;

class CatalogSwagger
{
    #[OA\Get(path: '/api/categories', summary: 'List categories with the number of active magazines', tags: ['Catalog'],
        responses: [new OA\Response(response: 200, description: 'Categories')])]
    public function categories(): void {}

    #[OA\Get(path: '/api/magazines', summary: 'Search and filter magazines', tags: ['Catalog'],
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'category', in: 'query', description: 'Category slug', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'format', in: 'query', schema: new OA\Schema(type: 'string', enum: ['print', 'digital', 'both'])),
            new OA\Parameter(name: 'sort', in: 'query', schema: new OA\Schema(type: 'string', enum: ['newest', 'price_asc', 'price_desc', 'title'])),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', maximum: 50)),
        ],
        responses: [new OA\Response(response: 200, description: 'Paginated magazines')])]
    public function magazines(): void {}

    #[OA\Get(path: '/api/magazines/{magazine}', summary: 'Magazine by id or slug', tags: ['Catalog'],
        parameters: [new OA\Parameter(name: 'magazine', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 200, description: 'Magazine'), new OA\Response(response: 404, description: 'Not found')])]
    public function magazine(): void {}
}
