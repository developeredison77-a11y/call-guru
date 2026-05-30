<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LanguageRequest extends FormRequest
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
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('languages', 'name')
                    ->whereNull('deleted_at')
                    ->ignore($this->route('language')),
            ],
            'status' => ['sometimes', 'integer', Rule::in([0, 1])],
        ];
    }
}
