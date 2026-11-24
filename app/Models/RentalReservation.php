<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentalReservation extends Model
{
    public const LABELS = ['pending' => 'Menunggu konfirmasi', 'confirmed' => 'Dikonfirmasi', 'playing' => 'Sedang bermain', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan', 'rejected' => 'Ditolak'];

    protected $guarded = [];

    protected function casts(): array
    {
        return ['starts_at' => 'immutable_datetime', 'ends_at' => 'immutable_datetime', 'duration_minutes' => 'integer', 'unit_rate' => 'integer', 'total_price' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class);
    }

    public function playSession(): BelongsTo
    {
        return $this->belongsTo(PlaySession::class);
    }

    public function extensions(): HasMany
    {
        return $this->hasMany(RentalExtensionRequest::class);
    }

    public function statusLabel(): string
    {
        if ($this->status === 'playing' && $this->playSession?->displayStatus() === 'expired') {
            return 'Waktu habis';
        }
        if ($this->status === 'playing' && $this->playSession?->status === 'paused') {
            return 'Sedang dijeda';
        }

        return self::LABELS[$this->status] ?? $this->status;
    }

    public function reference(): string
    {
        return 'PS-'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public function priceLabel(): string
    {
        return 'Rp'.number_format($this->total_price, 0, ',', '.');
    }
}
