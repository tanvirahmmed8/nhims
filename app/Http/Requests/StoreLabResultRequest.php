<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabResultRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasRole('lab_staff') === true; }

    public function rules(): array
    {
        return [
            'value' => ['required', 'string'],
            'unit' => ['nullable', 'string', 'max:50'],
            'reference_range' => ['nullable', 'string', 'max:100'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
