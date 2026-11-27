<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalIdentity extends Model
{
    protected $guarded = [];

    protected $hidden = ['encrypted_photo'];

    protected function casts(): array
    {
        return ['expires_at' => 'immutable_datetime', 'verified_at' => 'immutable_datetime'];
    }

    public function reservation()
    {
        return $this->belongsTo(RentalReservation::class, 'rental_reservation_id');
    }

    public static function purgeExpired(): int
    {
        return static::where('expires_at', '<=', now())->delete();
    }
}
