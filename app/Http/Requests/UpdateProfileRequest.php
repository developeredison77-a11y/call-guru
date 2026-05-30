<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'max:5'],
            'mobile_number' => [
                'required',
                'string',
                'max:10',
                Rule::unique('users', 'mobile_number')
                    ->where('country_code', $this->input('country_code'))
                    ->ignore($this->user()->id),
            ],
            'date_of_birth' => ['nullable', 'date'],
            'sex' => ['nullable', 'string', 'max:10'],
        ];
    }
}
