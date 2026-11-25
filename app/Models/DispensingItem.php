<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispensingItem extends Model
{
    protected $fillable = ['dispensing_id', 'prescription_item_id', 'quantity'];

    public function dispensing(): BelongsTo
    {
        return $this->belongsTo(Dispensing::class);
    }

    public function prescriptionItem(): BelongsTo
    {
        return $this->belongsTo(PrescriptionItem::class);
    }
}
