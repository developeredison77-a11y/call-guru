<?php

namespace App\Http\Requests\Auth;

use App\Enums\SexEnum;
use App\Support\PhoneNumbers\IndianMobileNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'countryCode' => IndianMobileNumber::countryCodeValidationRules(),
            'mobileNumber' => [
                ...IndianMobileNumber::validationRules(),
                Rule::unique('users', 'mobile_number')->where(
                    fn ($query) => $query->where('country_code', $this->input('countryCode'))
                ),
            ],
            'name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'sex' => ['nullable', Rule::in(SexEnum::values())],
            'language' => [
                'nullable',
                'integer',
                Rule::exists('languages', 'id')->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'countryCode.required' => 'Country code is required.',
            'countryCode.in' => 'Country code must be +91.',
            'mobileNumber.required' => 'Mobile number is required.',
            'mobileNumber.unique' => 'User already exists.',
            'date_of_birth.date' => 'Date of birth must be a valid ISO date string.',
            'date_of_birth.before_or_equal' => 'Date of birth cannot be in the future.',
            'sex.in' => 'Sex must be Male, Female, or Other.',
            'language.exists' => 'The selected language is invalid.',
        ];
    }
}
