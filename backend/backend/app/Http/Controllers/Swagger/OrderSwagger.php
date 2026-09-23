<?php

namespace App\Http\Controllers\Swagger;

use OpenApi\Attributes as OA;

class OrderSwagger
{
    #[OA\Get(path: '/api/orders', summary: 'My orders', tags: ['Orders'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Orders, newest first')])]
    public function index(): void {}

    #[OA\Post(path: '/api/orders', summary: 'Checkout the cart', tags: ['Orders'], security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 201, description: 'Order created'),
            new OA\Response(response: 409, description: 'Not enough stock'),
            new OA\Response(response: 422, description: 'Empty cart or invalid shipping details'),
        ])]
    public function store(): void {}

    #[OA\Get(path: '/api/orders/{order}', summary: 'Order details', tags: ['Orders'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Order with items'), new OA\Response(response: 404, description: 'Not found')])]
    public function show(): void {}

    #[OA\Post(path: '/api/orders/{order}/pay', summary: 'Pay for a pending order (mock)', tags: ['Orders'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Paid, now processing'), new OA\Response(response: 409, description: 'Cannot be paid')])]
    public function pay(): void {}

    #[OA\Post(path: '/api/orders/{order}/cancel', summary: 'Cancel a pending order and restock', tags: ['Orders'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Cancelled'), new OA\Response(response: 409, description: 'Order is no longer pending')])]
    public function cancel(): void {}
}
