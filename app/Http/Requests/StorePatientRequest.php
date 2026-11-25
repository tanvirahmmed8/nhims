<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['admin', 'receptionist']) === true;
    }

    public function rules(): array
    {
        return [
            'registered_at_facility_id' => ['required', 'exists:facilities,id'],
            'demo_national_id' => ['nullable', 'string', 'max:60', 'unique:patients,demo_national_id'],
            'name' => ['required', 'string', 'max:150'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'sex' => ['required', 'in:female,male,other'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:191'],
            'address' => ['required', 'string', 'max:255'],
            'allergies' => ['nullable', 'string'],
            'conditions' => ['nullable', 'string'],
            'emergency_name' => ['nullable', 'string', 'max:150'],
            'emergency_relationship' => ['nullable', 'string', 'max:80'],
            'emergency_phone' => ['nullable', 'string', 'max:30'],
            'emergency_address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
