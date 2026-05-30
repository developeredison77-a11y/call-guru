<?php

namespace App\Http\Requests;

use App\Enums\SexEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateManagedUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (int) $this->user()?->type === 1;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        $managedUser = $this->route('listener') ?? $this->route('guruji');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($managedUser),
            ],
            'country_code' => ['required', 'string', 'max:5'],
            'mobile_number' => [
                'required',
                'string',
                'max:10',
                Rule::unique('users', 'mobile_number')
                    ->where('country_code', $this->input('country_code'))
                    ->ignore($managedUser),
            ],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'sex' => ['nullable', Rule::in(SexEnum::values())],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function editableAttributes(): array
    {
        return $this->safe()->only([
            'name',
            'email',
            'country_code',
            'mobile_number',
            'date_of_birth',
            'sex',
        ]);
    }
}
