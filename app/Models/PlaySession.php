<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PlaySession extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['unit_number' => 'integer', 'ends_at' => 'immutable_datetime', 'finished_at' => 'immutable_datetime', 'total_seconds' => 'integer', 'remaining_seconds' => 'integer', 'hourly_rate' => 'integer'];
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class);
    }

    public function rentalReservation(): HasOne
    {
        return $this->hasOne(RentalReservation::class);
    }

    public function remaining(): int
    {
        return $this->status === 'running' ? max(0, $this->ends_at->getTimestamp() - now()->getTimestamp()) : ($this->status === 'paused' ? $this->remaining_seconds : 0);
    }

    public function displayStatus(): string
    {
        return $this->status === 'running' && $this->remaining() === 0 ? 'expired' : $this->status;
    }

    public function countdown(): string
    {
        $s = $this->remaining();

        return sprintf('%02d:%02d:%02d', intdiv($s, 3600), intdiv($s % 3600, 60), $s % 60);
    }

    public function estimatedPrice(): int
    {
        return $this->rentalReservation?->total_price ?? (int) round($this->total_seconds * $this->hourly_rate / 3600);
    }
}
