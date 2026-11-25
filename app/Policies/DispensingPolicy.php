<?php

namespace App\Policies;

use App\Models\Dispensing;
use App\Models\User;

class DispensingPolicy
{
    public function viewAny(User $user): bool { return $user->hasAnyRole(['admin', 'pharmacy_staff']); }
    public function view(User $user, Dispensing $dispensing): bool { return $user->hasRole('admin') || ($user->hasRole('pharmacy_staff') && $user->facilities()->whereKey($dispensing->facility_id)->exists()); }
    public function create(User $user): bool { return $user->hasRole('pharmacy_staff'); }
}
