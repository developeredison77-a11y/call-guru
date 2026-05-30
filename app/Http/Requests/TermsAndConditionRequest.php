<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TermsAndConditionRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'status' => ['sometimes', 'integer', Rule::in([0, 1])],
        ];
    }
}
