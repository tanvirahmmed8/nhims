<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    protected $fillable = ['actor_id', 'facility_id', 'action', 'auditable_type', 'auditable_id', 'outcome', 'metadata', 'ip_address', 'user_agent'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function scopeOutcome(Builder $query, string $outcome): Builder
    {
        return $query->where('outcome', $outcome);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}
