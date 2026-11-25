<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispensing extends Model
{
    protected $fillable = ['prescription_id', 'patient_id', 'facility_id', 'pharmacy_user_id', 'status', 'dispensed_at', 'notes'];

    protected function casts(): array
    {
        return ['dispensed_at' => 'datetime'];
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function pharmacyUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pharmacy_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DispensingItem::class);
    }
}
