<?php

namespace App\Http\Requests\Auth;

use App\Enums\SexEnum;
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
            'mobileNumber' => ['required', 'numeric', 'digits:10', 'regex:/^[6-9][0-9]{9}$/', 'unique:users,mobile_number'],
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

