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
            'mobileNumber' => IndianMobileNumber::validationRules(),
            'otp' => ['required', 'digits:4'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobileNumber.required' => 'Mobile number is required.',
            'mobileNumber.numeric' => 'Mobile number must be numeric.',
            'mobileNumber.digits' => 'Mobile number must be exactly 10 digits.',
            'mobileNumber.regex' => 'Please enter a valid Indian mobile number.',
            'otp.required' => 'OTP is required.',
            'otp.digits' => 'OTP must be a 4-digit code.',
        ];
    }
}
