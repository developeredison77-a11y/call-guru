<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Services\Auth\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly AuthService $authService) {}

    #[OA\Post(
        path: '/v1/auth/send-otp',
        summary: 'Send OTP',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['countryCode', 'mobileNumber'],
                properties: [
                    new OA\Property(property: 'countryCode', type: 'string', example: '+91'),
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
                        new OA\Property(property: 'otp', type: 'string', example: '123456'),
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
        $otp = $this->authService->sendOtp(
            $request->string('countryCode')->toString(),
            $request->string('mobileNumber')->toString()
        );

        return $this->successResponse([
            'otp' => $otp,
        ], 'OTP sent successfully');
    }

    #[OA\Post(
        path: '/v1/auth/verify-otp',
        summary: 'Verify OTP',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['countryCode', 'mobileNumber', 'otp'],
                properties: [
                    new OA\Property(property: 'countryCode', type: 'string', example: '+91'),
                    new OA\Property(property: 'mobileNumber', type: 'string', example: '9876543210'),
                    new OA\Property(property: 'otp', type: 'string', example: '123456'),
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
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Rahul'),
                                new OA\Property(property: 'accessToken', type: 'string', example: '1|sanctum_access_token_here'),
                                new OA\Property(property: 'refreshToken', type: 'string', example: '2|sanctum_refresh_token_here'),
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
                        new OA\Property(property: 'message', type: 'string', example: 'Invalid OTP'),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Inactive user account',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Your account is inactive. Please contact the administrator.'),
                    ]
                )
            ),
        ]
    )]
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->authService->verifyOtp(
            $request->string('countryCode')->toString(),
            $request->string('mobileNumber')->toString(),
            $request->string('otp')->toString()
        );

        if ($result['isNewUser'] === true) {
            return $this->successResponse([
                'isNewUser' => true,
            ], $result['message']);
        }

        return $this->successResponse([
            'id' => $result['user']->id,
            'name' => $result['user']->name,
            'accessToken' => $result['accessToken'],
            'refreshToken' => $result['refreshToken'],
        ]);
    }

    #[OA\Post(
        path: '/v1/auth/register',
        summary: 'Register user',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['countryCode', 'mobileNumber', 'name'],
                properties: [
                    new OA\Property(property: 'countryCode', type: 'string', example: '+91'),
                    new OA\Property(property: 'mobileNumber', type: 'string', example: '9876543210'),
                    new OA\Property(property: 'name', type: 'string', example: 'Rahul'),
                    new OA\Property(property: 'date_of_birth', type: 'string', format: 'date-time', example: '1996-05-28T00:00:00.000Z'),
                    new OA\Property(property: 'sex', type: 'string', enum: ['Male', 'Female', 'Other'], example: 'Male'),
                    new OA\Property(
                        property: 'languages',
                        type: 'array',
                        nullable: true,
                        items: new OA\Items(type: 'integer'),
                        example: [1, 2]
                    ),
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
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Test User'),
                        new OA\Property(property: 'accessToken', type: 'string', example: '7|sanctum_access_token_here'),
                        new OA\Property(property: 'refreshToken', type: 'string', example: '8|sanctum_refresh_token_here'),
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
            'id' => $result['user']->id,
            'name' => $result['user']->name,
            'accessToken' => $result['accessToken'],
            'refreshToken' => $result['refreshToken'],
        ]);
    }

    #[OA\Post(
        path: '/v1/auth/refresh-token',
        summary: 'Refresh access token',
        security: [['sanctum' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token refreshed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Success'),
                        new OA\Property(property: 'accessToken', type: 'string', example: '9|sanctum_access_token_here'),
                        new OA\Property(property: 'refreshToken', type: 'string', example: '10|sanctum_refresh_token_here'),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Invalid refresh token or inactive account',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Invalid refresh token.'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function refreshToken(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        if (! $token instanceof PersonalAccessToken) {
            throw new ApiException('Invalid refresh token.', 403);
        }

        $result = $this->authService->refreshToken($token);

        return $this->successResponse([
            'accessToken' => $result['accessToken'],
            'refreshToken' => $result['refreshToken'],
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
