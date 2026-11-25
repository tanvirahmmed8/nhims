<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabTest extends Model
{
    protected $fillable = ['code', 'name', 'specimen_type', 'unit', 'reference_range', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(LabOrderItem::class);
    }
}
