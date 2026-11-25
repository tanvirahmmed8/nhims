<?php

namespace App\Policies;

use App\Models\Consultation;
use App\Models\User;

class ConsultationPolicy
{
    public function viewAny(User $user): bool { return $user->hasAnyRole(['admin', 'doctor', 'patient']); }
    public function view(User $user, Consultation $consultation): bool
    {
        return $user->hasRole('admin') || ($user->hasRole('doctor') && $user->id === $consultation->doctor_id) || ($user->hasRole('patient') && $user->patient_id === $consultation->patient_id);
    }
    public function create(User $user): bool { return $user->hasRole('doctor'); }
    public function update(User $user, Consultation $consultation): bool { return $user->hasRole('doctor') && $user->id === $consultation->doctor_id; }
}
