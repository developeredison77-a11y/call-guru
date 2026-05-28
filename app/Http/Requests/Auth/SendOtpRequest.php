<?php

namespace App\Http\Requests\Auth;

use App\Support\PhoneNumbers\IndianMobileNumber;
use Illuminate\Foundation\Http\FormRequest;

class SendOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mobileNumber' => IndianMobileNumber::validationRules(),
        ];
    }

    public function messages(): array
    {
        return [
            'mobileNumber.required' => 'Mobile number is required.',
            'mobileNumber.numeric' => 'Mobile number must be numeric.',
            'mobileNumber.digits' => 'Mobile number must be exactly 10 digits.',
            'mobileNumber.regex' => 'Please enter a valid Indian mobile number.',
        ];
    }
}
