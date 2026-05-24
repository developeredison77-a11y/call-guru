<?php

namespace App\Http\Requests\Auth;

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
            'mobileNumber' => ['required', 'numeric', 'digits:10', 'regex:/^[6-9][0-9]{9}$/'],
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

