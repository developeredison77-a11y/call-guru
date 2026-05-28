<?php

namespace App\Repositories\Auth;

use App\Models\OtpVerification;
use Carbon\Carbon;

class OtpVerificationRepository
{
    public function findByMobileNumber(string $countryCode, string $mobileNumber): ?OtpVerification
    {
        return OtpVerification::query()
            ->where('country_code', $countryCode)
            ->where('mobile_number', $mobileNumber)
            ->first();
    }

    public function upsertOtp(string $countryCode, string $mobileNumber, string $hashedOtp, Carbon $expiresAt): OtpVerification
    {
        return OtpVerification::query()->updateOrCreate(
            [
                'country_code' => $countryCode,
                'mobile_number' => $mobileNumber,
            ],
            [
                'otp' => $hashedOtp,
                'expires_at' => $expiresAt,
                'is_verified' => false,
                'verified_at' => null,
            ]
        );
    }

    public function markVerified(OtpVerification $otpVerification, Carbon $verifiedAt): bool
    {
        return $otpVerification->update([
            'is_verified' => true,
            'verified_at' => $verifiedAt,
        ]);
    }
}
