<?php

namespace App\Support;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'Authentication APIs for mobile OTP flow.',
    title: 'Call Guru API'
)]
#[OA\Server(
    url: '/api',
    description: 'API Base URL'
)]
#[OA\Tag(
    name: 'Auth',
    description: 'OTP based authentication endpoints'
)]
#[OA\Tag(
    name: 'Languages',
    description: 'Public language endpoints'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token'
)]
class OpenApi {}
