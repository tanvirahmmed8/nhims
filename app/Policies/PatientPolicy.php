<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist', 'doctor', 'patient']);
    }

    public function view(User $user, Patient $patient): bool
    {
        if ($user->hasRole('patient')) {
            return $user->patient_id === $patient->id;
        }

        if ($user->hasAnyRole(['admin', 'receptionist'])) {
            return $user->facilities()->whereKey($patient->registered_at_facility_id)->exists() || $user->hasRole('admin');
        }

        if ($user->hasRole('doctor')) {
            return $patient->appointments()->where('doctor_id', $user->id)->exists()
                || $user->facilities()->whereKey($patient->registered_at_facility_id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist']);
    }

    public function update(User $user, Patient $patient): bool
    {
        return $this->create($user) && $this->view($user, $patient);
    }
}
