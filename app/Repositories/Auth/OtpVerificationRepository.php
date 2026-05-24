<?php

namespace App\Repositories\Auth;

use App\Models\OtpVerification;
use Carbon\Carbon;

class OtpVerificationRepository
{
    public function findByMobileNumber(string $mobileNumber): ?OtpVerification
    {
        return OtpVerification::query()->where('mobile_number', $mobileNumber)->first();
    }

    public function upsertOtp(string $mobileNumber, string $hashedOtp, Carbon $expiresAt): OtpVerification
    {
        return OtpVerification::query()->updateOrCreate(
            ['mobile_number' => $mobileNumber],
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

