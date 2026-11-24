<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalExtensionRequest extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['minutes' => 'integer', 'decided_at' => 'immutable_datetime'];
    }

    public function rentalReservation(): BelongsTo
    {
        return $this->belongsTo(RentalReservation::class);
    }
}
