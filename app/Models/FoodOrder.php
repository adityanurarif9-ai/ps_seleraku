<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodOrder extends Model
{
    public const LABELS = ['pending' => 'Menunggu konfirmasi', 'preparing' => 'Sedang disiapkan', 'ready' => 'Siap diambil', 'completed' => 'Selesai', 'rejected' => 'Ditolak', 'cancelled' => 'Dibatalkan'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['total_price' => 'integer'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(FoodOrderItem::class);
    }

    public function reference(): string
    {
        return 'KD-'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public function statusLabel(): string
    {
        return __(self::LABELS[$this->status] ?? $this->status);
    }
}
