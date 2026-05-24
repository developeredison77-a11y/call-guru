<?php

use App\Http\Controllers\API\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/send-otp', [AuthController::class, 'sendOtp']);
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/register', [AuthController::class, 'register']);

        Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    });
});

