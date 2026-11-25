<?php

namespace App\Policies;

use App\Models\LabOrder;
use App\Models\User;

class LabOrderPolicy
{
    public function viewAny(User $user): bool { return $user->hasAnyRole(['admin', 'doctor', 'lab_staff', 'patient']); }
    public function view(User $user, LabOrder $order): bool
    {
        return $user->hasRole('admin') || ($user->hasRole('doctor') && $user->id === $order->doctor_id) || ($user->hasRole('lab_staff') && $user->facilities()->whereKey($order->facility_id)->exists()) || ($user->hasRole('patient') && $user->patient_id === $order->patient_id);
    }
    public function create(User $user): bool { return $user->hasRole('doctor'); }
}
