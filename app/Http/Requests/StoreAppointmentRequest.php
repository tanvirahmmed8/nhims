<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['admin', 'receptionist']) === true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'facility_id' => ['required', 'exists:facilities,id'],
            'doctor_id' => ['required', 'exists:users,id'],
            'scheduled_at' => ['required', 'date', 'after_or_equal:today'],
            'type' => ['required', 'string', 'max:60'],
            'reason' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:booked,confirmed'],
        ];
    }
}
