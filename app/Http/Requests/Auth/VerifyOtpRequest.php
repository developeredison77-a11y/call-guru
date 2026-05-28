<?php

namespace App\Http\Requests\Auth;

use App\Support\PhoneNumbers\IndianMobileNumber;
use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'countryCode' => IndianMobileNumber::countryCodeValidationRules(),
            'mobileNumber' => IndianMobileNumber::validationRules(),
            'otp' => ['required', 'digits:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'countryCode.required' => 'Country code is required.',
            'countryCode.in' => 'Country code must be +91.',
            'mobileNumber.required' => 'Mobile number is required.',
            'mobileNumber.numeric' => 'Mobile number must be numeric.',
            'mobileNumber.digits' => 'Mobile number must be exactly 10 digits.',
            'mobileNumber.regex' => 'Please enter a valid Indian mobile number.',
            'otp.required' => 'OTP is required.',
            'otp.digits' => 'OTP must be a 6-digit code.',
        ];
    }
}
