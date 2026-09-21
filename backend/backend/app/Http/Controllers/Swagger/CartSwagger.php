<?php

namespace App\Http\Controllers\Swagger;

use OpenApi\Attributes as OA;

class CartSwagger
{
    #[OA\Get(path: '/api/cart', summary: 'Cart with totals and shipping', tags: ['Cart'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Cart summary')])]
    public function show(): void {}

    #[OA\Post(path: '/api/cart', summary: 'Add a magazine (quantity adds up)', tags: ['Cart'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Updated cart'), new OA\Response(response: 409, description: 'Not enough stock')])]
    public function store(): void {}

    #[OA\Delete(path: '/api/cart', summary: 'Empty the cart', tags: ['Cart'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Empty cart')])]
    public function clear(): void {}

    #[OA\Put(path: '/api/cart/{magazine}', summary: 'Set quantity (0 removes the line)', tags: ['Cart'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Updated cart'), new OA\Response(response: 409, description: 'Not enough stock')])]
    public function update(): void {}

    #[OA\Delete(path: '/api/cart/{magazine}', summary: 'Remove a line', tags: ['Cart'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Updated cart')])]
    public function destroy(): void {}
}
