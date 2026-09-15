<?php

namespace App\Http\Controllers\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0.0', title: 'Magazine Shop API', description: 'REST API of the magazine shop. Money values are integer cents.')]
#[OA\Server(url: '/', description: 'Current host')]
#[OA\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', scheme: 'bearer', bearerFormat: 'JWT')]
class OpenApi
{
    //
}
