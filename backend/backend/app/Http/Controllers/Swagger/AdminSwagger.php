<?php

namespace App\Http\Controllers\Swagger;

use OpenApi\Attributes as OA;

class AdminSwagger
{
    #[OA\Get(path: '/api/admin/stats', summary: 'Dashboard numbers, revenue chart and low stock', tags: ['Admin'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Statistics'), new OA\Response(response: 403, description: 'Not an administrator')])]
    public function stats(): void {}

    #[OA\Get(path: '/api/admin/magazines', summary: 'All magazines, including inactive', tags: ['Admin'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Paginated magazines')])]
    public function magazines(): void {}

    #[OA\Post(path: '/api/admin/magazines', summary: 'Create a magazine', tags: ['Admin'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 201, description: 'Created'), new OA\Response(response: 422, description: 'Validation error')])]
    public function createMagazine(): void {}

    #[OA\Put(path: '/api/admin/magazines/{magazine}', summary: 'Update a magazine (partial)', tags: ['Admin'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateMagazine(): void {}

    #[OA\Delete(path: '/api/admin/magazines/{magazine}', summary: 'Deactivate a magazine (soft delete)', tags: ['Admin'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 204, description: 'Deactivated')])]
    public function deleteMagazine(): void {}

    #[OA\Post(path: '/api/admin/categories', summary: 'Create a category', tags: ['Admin'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createCategory(): void {}

    #[OA\Put(path: '/api/admin/categories/{category}', summary: 'Rename a category', tags: ['Admin'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateCategory(): void {}

    #[OA\Delete(path: '/api/admin/categories/{category}', summary: 'Delete a category', tags: ['Admin'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 204, description: 'Deleted')])]
    public function deleteCategory(): void {}

    #[OA\Get(path: '/api/admin/orders', summary: 'All orders, optionally filtered by status', tags: ['Admin'], security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 200, description: 'Paginated orders')])]
    public function orders(): void {}

    #[OA\Patch(path: '/api/admin/orders/{order}/status', summary: 'Move an order to the next state', tags: ['Admin'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Updated order'), new OA\Response(response: 409, description: 'Transition not allowed')])]
    public function updateOrderStatus(): void {}
}
