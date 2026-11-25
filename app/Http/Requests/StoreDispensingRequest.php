<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDispensingRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasRole('pharmacy_staff') === true; }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.prescription_item_id' => ['required', 'integer', 'exists:prescription_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
