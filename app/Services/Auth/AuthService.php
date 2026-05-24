<?php

namespace App\Services\Auth;

use App\Exceptions\ApiException;
use App\Helpers\OtpHelper;
use App\Repositories\Auth\OtpVerificationRepository;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(private readonly OtpVerificationRepository $otpRepository)
    {
    }

    public function sendOtp(string $mobileNumber): void
    {
        if (! $this->isAllowedMobileNumber($mobileNumber)) {
            throw new ApiException('Invalid mobile number', 422);
        }

        $otp = OtpHelper::generate(4);
        $expiresAt = Carbon::now()->addMinutes(5);

        $this->otpRepository->upsertOtp($mobileNumber, Hash::make($otp), $expiresAt);
    }

    public function verifyOtp(string $mobileNumber, string $otp): array
    {
        $otpVerification = $this->otpRepository->findByMobileNumber($mobileNumber);

        if (! $otpVerification || $otpVerification->expires_at->isPast() || ! Hash::check($otp, $otpVerification->otp)) {
            throw new ApiException('Invalid or expired OTP', 422);
        }

        DB::transaction(function () use ($otpVerification) {
            $this->otpRepository->markVerified($otpVerification, Carbon::now());
        });

        $user = User::query()->where('mobile_number', $mobileNumber)->first();

        if (! $user) {
            return [
                'isNewUser' => true,
                'message' => 'User registration required',
            ];
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'isNewUser' => false,
            'token' => $token,
            'user' => $user,
        ];
    }

    public function register(array $payload): array
    {
        $mobileNumber = $payload['mobileNumber'];

        if (User::query()->where('mobile_number', $mobileNumber)->exists()) {
            throw new ApiException('User already exists', 409);
        }

        $otpVerification = $this->otpRepository->findByMobileNumber($mobileNumber);

        if (! $otpVerification || ! $otpVerification->is_verified) {
            throw new ApiException('Verified OTP required before registration', 422);
        }

        $user = DB::transaction(function () use ($payload, $mobileNumber) {
            return User::query()->create([
                'name' => $payload['name'],
                'mobile_number' => $mobileNumber,
                'age' => $payload['age'] ?? null,
                'sex' => $payload['sex'] ?? null,
            ]);
        });

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    private function isAllowedMobileNumber(string $mobileNumber): bool
    {
        return preg_match('/^[6-9][0-9]{9}$/', $mobileNumber) === 1;
    }
}

