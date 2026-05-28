<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\Auth\UserResource;
use App\Services\Auth\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly AuthService $authService)
    {
    }

    #[OA\Post(
        path: '/v1/auth/send-otp',
        summary: 'Send OTP',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['mobileNumber'],
                properties: [
                    new OA\Property(property: 'mobileNumber', type: 'string', example: '9876543210'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'OTP sent',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'OTP sent successfully'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Invalid mobile',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Invalid mobile number'),
                    ]
                )
            ),
        ]
    )]
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $this->authService->sendOtp($request->string('mobileNumber')->toString());

        return $this->successResponse(message: 'OTP sent successfully');
    }

    #[OA\Post(
        path: '/v1/auth/verify-otp',
        summary: 'Verify OTP',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['mobileNumber', 'otp'],
                properties: [
                    new OA\Property(property: 'mobileNumber', type: 'string', example: '9876543210'),
                    new OA\Property(property: 'otp', type: 'string', example: '1234'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'OTP verified',
                content: new OA\JsonContent(
                    oneOf: [
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'success', type: 'boolean', example: true),
                                new OA\Property(property: 'message', type: 'string', example: 'Success'),
                                new OA\Property(property: 'isNewUser', type: 'boolean', example: false),
                                new OA\Property(property: 'token', type: 'string', example: '1|sanctum_token_here'),
                                new OA\Property(
                                    property: 'user',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', example: 1),
                                        new OA\Property(property: 'name', type: 'string', example: 'Rahul'),
                                        new OA\Property(property: 'mobileNumber', type: 'string', example: '9876543210'),
                                        new OA\Property(property: 'age', type: 'integer', example: 30),
                                        new OA\Property(property: 'sex', type: 'string', example: 'Male'),
                                    ],
                                    type: 'object'
                                ),
                            ],
                            type: 'object'
                        ),
                        new OA\Schema(
                            properties: [
                                new OA\Property(property: 'success', type: 'boolean', example: true),
                                new OA\Property(property: 'message', type: 'string', example: 'User registration required'),
                                new OA\Property(property: 'isNewUser', type: 'boolean', example: true),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Invalid or expired OTP',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Invalid or expired OTP'),
                    ]
                )
            ),
        ]
    )]
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->authService->verifyOtp(
            $request->string('mobileNumber')->toString(),
            $request->string('otp')->toString()
        );

        if ($result['isNewUser'] === true) {
            return $this->successResponse([
                'isNewUser' => true,
            ], $result['message']);
        }

        return $this->successResponse([
            'isNewUser' => false,
            'token' => $result['token'],
            'user' => new UserResource($result['user']),
        ]);
    }

    #[OA\Post(
        path: '/v1/auth/register',
        summary: 'Register user',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['mobileNumber', 'name'],
                properties: [
                    new OA\Property(property: 'mobileNumber', type: 'string', example: '9876543210'),
                    new OA\Property(property: 'name', type: 'string', example: 'Rahul'),
                    new OA\Property(property: 'age', type: 'integer', example: 30),
                    new OA\Property(property: 'sex', type: 'string', enum: ['Male', 'Female', 'Other'], example: 'Male'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Registered',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Success'),
                        new OA\Property(property: 'token', type: 'string', example: '1|sanctum_token_here'),
                        new OA\Property(
                            property: 'user',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Rahul'),
                                new OA\Property(property: 'mobileNumber', type: 'string', example: '9876543210'),
                                new OA\Property(property: 'age', type: 'integer', example: 30),
                                new OA\Property(property: 'sex', type: 'string', example: 'Male'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 409,
                description: 'Already exists',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'User already exists'),
                    ]
                )
            ),
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return $this->successResponse([
            'token' => $result['token'],
            'user' => new UserResource($result['user']),
        ]);
    }

    #[OA\Post(
        path: '/v1/auth/logout',
        summary: 'Logout',
        security: [['sanctum' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logged out',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Logged out successfully'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->successResponse(message: 'Logged out successfully');
    }
}
