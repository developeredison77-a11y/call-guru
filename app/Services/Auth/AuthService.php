<?php

namespace App\Services\Auth;

use App\Exceptions\ApiException;
use App\Helpers\OtpHelper;
use App\Repositories\Auth\OtpVerificationRepository;
use App\Repositories\Auth\UserRepository;
use App\Support\PhoneNumbers\IndianMobileNumber;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    private const OTP_LENGTH = 4;

    private const OTP_TTL_MINUTES = 5;

    private const TOKEN_NAME = 'auth_token';

    public function __construct(
        private readonly OtpVerificationRepository $otpRepository,
        private readonly UserRepository $userRepository,
    ) {}

    public function sendOtp(string $mobileNumber): void
    {
        if (! IndianMobileNumber::isValid($mobileNumber)) {
            throw new ApiException('Invalid mobile number', 422);
        }

        $otp = OtpHelper::generate(self::OTP_LENGTH);
        $expiresAt = Carbon::now()->addMinutes(self::OTP_TTL_MINUTES);

        $this->otpRepository->upsertOtp($mobileNumber, Hash::make($otp), $expiresAt);
    }

    public function verifyOtp(string $mobileNumber, string $otp): array
    {
        $otpVerification = $this->otpRepository->findByMobileNumber($mobileNumber);

        if (! $otpVerification || $otpVerification->expires_at->isPast() || ! Hash::check($otp, $otpVerification->otp)) {
            throw new ApiException('Invalid or expired OTP', 422);
        }

        DB::transaction(function () use ($otpVerification): void {
            $this->otpRepository->markVerified($otpVerification, Carbon::now());
        });

        $user = $this->userRepository->findByMobileNumber($mobileNumber);

        if (! $user) {
            return [
                'isNewUser' => true,
                'message' => 'User registration required',
            ];
        }

        $token = $user->createToken(self::TOKEN_NAME)->plainTextToken;

        return [
            'isNewUser' => false,
            'token' => $token,
            'user' => $user,
        ];
    }

    public function register(array $payload): array
    {
        $mobileNumber = $payload['mobileNumber'];

        if ($this->userRepository->existsByMobileNumber($mobileNumber)) {
            throw new ApiException('User already exists', 409);
        }

        $otpVerification = $this->otpRepository->findByMobileNumber($mobileNumber);

        if (! $otpVerification || ! $otpVerification->is_verified) {
            throw new ApiException('Verified OTP required before registration', 422);
        }

        $user = DB::transaction(function () use ($payload, $mobileNumber) {
            return $this->userRepository->create([
                'name' => $payload['name'],
                'mobile_number' => $mobileNumber,
                'age' => $payload['age'] ?? null,
                'sex' => $payload['sex'] ?? null,
            ]);
        });

        $token = $user->createToken(self::TOKEN_NAME)->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

}
