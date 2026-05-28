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
    private const OTP_LENGTH = 6;

    private const OTP_TTL_MINUTES = 5;

    private const TOKEN_NAME = 'auth_token';

    public function __construct(
        private readonly OtpVerificationRepository $otpRepository,
        private readonly UserRepository $userRepository,
    ) {}

    public function sendOtp(string $countryCode, string $mobileNumber): string
    {
        if (! IndianMobileNumber::isValidCountryCode($countryCode) || ! IndianMobileNumber::isValid($mobileNumber)) {
            throw new ApiException('Invalid mobile number', 422);
        }

        $otp = OtpHelper::generate(self::OTP_LENGTH);
        $expiresAt = Carbon::now()->addMinutes(self::OTP_TTL_MINUTES);

        $this->otpRepository->upsertOtp($countryCode, $mobileNumber, Hash::make($otp), $expiresAt);

        return $otp;
    }

    public function verifyOtp(string $countryCode, string $mobileNumber, string $otp): array
    {
        $otpVerification = $this->otpRepository->findByMobileNumber($countryCode, $mobileNumber);

        if (! $otpVerification) {
            throw new ApiException('Invalid OTP', 422);
        }

        if (! Hash::check($otp, $otpVerification->otp)) {
            throw new ApiException('Invalid OTP', 422);
        }

        if ($otpVerification->expires_at->isPast()) {
            throw new ApiException('OTP expired', 422);
        }

        DB::transaction(function () use ($otpVerification): void {
            $this->otpRepository->markVerified($otpVerification, Carbon::now());
        });

        $user = $this->userRepository->findByMobileNumber($countryCode, $mobileNumber);

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
        $countryCode = $payload['countryCode'];

        if ($this->userRepository->existsByMobileNumber($countryCode, $mobileNumber)) {
            throw new ApiException('User already exists', 409);
        }

        $otpVerification = $this->otpRepository->findByMobileNumber($countryCode, $mobileNumber);

        if (! $otpVerification || ! $otpVerification->is_verified) {
            throw new ApiException('Verified OTP required before registration', 422);
        }

        $user = DB::transaction(function () use ($payload, $countryCode, $mobileNumber) {
            return $this->userRepository->create([
                'name' => $payload['name'],
                'country_code' => $countryCode,
                'mobile_number' => $mobileNumber,
                'date_of_birth' => isset($payload['date_of_birth'])
                    ? Carbon::parse($payload['date_of_birth'])->toDateString()
                    : null,
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
