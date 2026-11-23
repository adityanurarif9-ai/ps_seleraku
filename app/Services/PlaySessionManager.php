<?php

namespace App\Services;

use App\Models\CatalogItem;
use App\Models\PlaySession;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlaySessionManager
{
    private function authorize(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }

    public function start(array $data): PlaySession
    {
        $this->authorize();
        $data = validator($data, ['catalog_item_id' => 'required|integer', 'customer_name' => 'required|string|max:100', 'minutes' => 'required|integer|min:1|max:1440'])->validate();

        return DB::transaction(function () use ($data) {
            $item = CatalogItem::lockForUpdate()->findOrFail($data['catalog_item_id']);
            if ($item->category !== 'Konsol') {
                throw ValidationException::withMessages(['catalog_item_id' => 'Pilih item kategori Konsol.']);
            }
            $occupied = PlaySession::where('catalog_item_id', $item->id)->where(function ($q) {
                $q->where('status', 'paused')->orWhere(fn ($r) => $r->where('status', 'running')->where('ends_at', '>', now()));
            })->count();
            if ($occupied >= $item->stock) {
                throw ValidationException::withMessages(['catalog_item_id' => 'Semua unit konsol ini sedang dipakai.']);
            }
            $seconds = $data['minutes'] * 60;

            return PlaySession::create(['catalog_item_id' => $item->id, 'created_by' => auth()->id(), 'customer_name' => trim($data['customer_name']), 'total_seconds' => $seconds, 'remaining_seconds' => $seconds, 'hourly_rate' => $item->price, 'status' => 'running', 'ends_at' => now()->addSeconds($seconds)]);
        });
    }

    public function change(PlaySession $session, string $action, int $minutes = 0): PlaySession
    {
        $this->authorize();

        return DB::transaction(function () use ($session, $action, $minutes) {
            CatalogItem::whereKey($session->catalog_item_id)->lockForUpdate()->firstOrFail();
            $s = PlaySession::lockForUpdate()->findOrFail($session->id);
            if ($s->status === 'finished') {
                return $s;
            }
            $remaining = $s->remaining();
            if ($action === 'finish') {
                $s->remaining_seconds = $remaining;
                $s->status = 'finished';
                $s->ends_at = null;
                $s->finished_at = now();
            } elseif ($remaining === 0) {
                throw ValidationException::withMessages(['session' => 'Waktu sudah habis. Akhiri sesi ini lalu mulai sesi baru.']);
            } elseif ($action === 'pause' && $s->status === 'running') {
                $s->remaining_seconds = $remaining;
                $s->status = 'paused';
                $s->ends_at = null;
            } elseif ($action === 'resume' && $s->status === 'paused') {
                $s->status = 'running';
                $s->ends_at = now()->addSeconds($remaining);
            } elseif ($action === 'extend') {
                if ($minutes < 1 || $minutes > 1440 || $s->total_seconds + $minutes * 60 > 604800) {
                    throw ValidationException::withMessages(['minutes' => 'Tambahan waktu 1?1440 menit, total maksimal 7 hari.']);
                }
                $s->total_seconds += $minutes * 60;
                $s->remaining_seconds = $remaining + $minutes * 60;
                if ($s->status === 'running') {
                    $s->ends_at = now()->addSeconds($s->remaining_seconds);
                }
            }
            $s->save();

            return $s;
        });
    }
}
