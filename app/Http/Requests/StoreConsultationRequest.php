<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasRole('doctor') === true; }

    public function rules(): array
    {
        return [
            'symptoms' => ['nullable', 'string'],
            'examination' => ['nullable', 'string'],
            'diagnosis' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'follow_up' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,completed'],
        ];
    }
}
