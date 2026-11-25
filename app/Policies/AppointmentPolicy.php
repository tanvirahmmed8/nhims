<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist', 'doctor', 'patient']);
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('patient')) {
            return $user->patient_id === $appointment->patient_id;
        }

        if ($user->hasRole('doctor')) {
            return $user->id === $appointment->doctor_id;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->hasRole('receptionist')
            && $user->facilities()->whereKey($appointment->facility_id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist']);
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist']) && $this->view($user, $appointment);
    }
}
