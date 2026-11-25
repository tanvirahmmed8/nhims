<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrescriptionItem extends Model
{
    protected $fillable = ['prescription_id', 'medicine_name', 'dose', 'route', 'frequency', 'duration', 'quantity', 'instructions'];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function dispensingItems(): HasMany
    {
        return $this->hasMany(DispensingItem::class);
    }
}
