<?php

namespace App\Http\Controllers\Swagger;

use OpenApi\Attributes as OA;

class AuthSwagger
{
    #[OA\Post(path: '/api/auth/register', summary: 'Register a customer', tags: ['Auth'],
        responses: [new OA\Response(response: 201, description: 'Registered, returns a JWT'), new OA\Response(response: 422, description: 'Validation error')])]
    public function register(): void {}

    #[OA\Post(path: '/api/auth/login', summary: 'Log in', tags: ['Auth'],
        responses: [new OA\Response(response: 200, description: 'Returns a JWT'), new OA\Response(response: 401, description: 'Invalid credentials')])]
    public function login(): void {}

    #[OA\Post(path: '/api/auth/refresh', summary: 'Exchange the token for a new one', tags: ['Auth'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'New JWT'), new OA\Response(response: 401, description: 'Token cannot be refreshed')])]
    public function refresh(): void {}

    #[OA\Post(path: '/api/auth/logout', summary: 'Invalidate the current token', tags: ['Auth'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 204, description: 'Logged out')])]
    public function logout(): void {}

    #[OA\Get(path: '/api/auth/me', summary: 'Current user', tags: ['Auth'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'User profile')])]
    public function me(): void {}

    #[OA\Put(path: '/api/auth/me', summary: 'Update name and/or email', tags: ['Auth'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Updated profile'), new OA\Response(response: 422, description: 'Validation error')])]
    public function updateProfile(): void {}

    #[OA\Put(path: '/api/auth/password', summary: 'Change password', tags: ['Auth'], security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 204, description: 'Password changed'), new OA\Response(response: 422, description: 'Wrong current password')])]
    public function changePassword(): void {}
}
