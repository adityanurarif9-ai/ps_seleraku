<?php

namespace App\Models;

use chillerlan\QRCode\QRCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class TestPayment extends Model
{
    public const METHODS = ['qris' => 'QRIS', 'dana' => 'DANA', 'gopay' => 'GoPay', 'ovo' => 'OVO', 'bank' => 'Transfer Bank'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['amount' => 'integer', 'simulated_at' => 'datetime'];
    }

    public function rentalReservation()
    {
        return $this->belongsTo(RentalReservation::class);
    }

    public function foodOrder()
    {
        return $this->belongsTo(FoodOrder::class);
    }

    public function reference(): string
    {
        return 'UJI-'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public function demoScanUrl(): string
    {
        return URL::temporarySignedRoute('payments.scan', now()->addMinutes(30), ['payment' => $this->id]);
    }

    public function demoQr(?string $scanUrl = null): string
    {
        // Opens a short-lived demo page; never an EMV/QRIS payment payload.
        return (new QRCode)->render($scanUrl ?? $this->demoScanUrl());
    }

    public function statusLabel(): string
    {
        return __(match ($this->status) {
            'simulated_paid' => 'Simulasi berhasil','failed' => 'Simulasi gagal',default => 'Belum dibayar (uji)'
        });
    }
}
