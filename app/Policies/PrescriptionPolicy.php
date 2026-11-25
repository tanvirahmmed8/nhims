<?php

namespace App\Policies;

use App\Models\Prescription;
use App\Models\User;

class PrescriptionPolicy
{
    public function viewAny(User $user): bool { return $user->hasAnyRole(['admin', 'doctor', 'pharmacy_staff', 'patient']); }
    public function view(User $user, Prescription $prescription): bool
    {
        return $user->hasRole('admin') || ($user->hasRole('doctor') && $user->id === $prescription->doctor_id) || ($user->hasRole('pharmacy_staff') && $user->facilities()->whereKey($prescription->facility_id)->exists()) || ($user->hasRole('patient') && $user->patient_id === $prescription->patient_id);
    }
    public function create(User $user): bool { return $user->hasRole('doctor'); }
    public function update(User $user, Prescription $prescription): bool { return false; }
}
