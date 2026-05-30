<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
                Rule::unique('categories', 'name')
                    ->whereNull('deleted_at')
                    ->ignore($this->route('category')),
            ],
            'status' => ['sometimes', 'integer', Rule::in([0, 1])],
        ];
    }
}
