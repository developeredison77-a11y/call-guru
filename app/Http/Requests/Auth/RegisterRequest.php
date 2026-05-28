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
            'mobileNumber' => IndianMobileNumber::validationRules(unique: true),
            'name' => ['required', 'string', 'max:100'],
            'age' => ['nullable', 'integer', 'min:1', 'max:120'],
            'sex' => ['nullable', Rule::in(SexEnum::values())],
        ];
    }

    public function messages(): array
    {
        return [
            'mobileNumber.required' => 'Mobile number is required.',
            'mobileNumber.unique' => 'User already exists.',
            'sex.in' => 'Sex must be Male, Female, or Other.',
        ];
    }
}
