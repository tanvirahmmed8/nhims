<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabOrderRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasRole('doctor') === true; }

    public function rules(): array
    {
        return [
            'test_ids' => ['required', 'array', 'min:1'],
            'test_ids.*' => ['integer', 'exists:lab_tests,id'],
            'priority' => ['required', 'in:routine,urgent'],
            'sample_type' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
