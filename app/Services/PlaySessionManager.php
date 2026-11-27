<?php

namespace App\Services;

use App\Models\CatalogItem;
use App\Models\PlaySession;
use App\Models\RentalReservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlaySessionManager
{
    private function authorize(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }

    public function start(array $data, ?RentalReservation $reservation = null): PlaySession
    {
        $this->authorize();
        if ($reservation && ! $reservation->fresh()->paymentReady()) {
            throw ValidationException::withMessages(['payment' => 'Selesaikan pembayaran demo sebelum mulai sesi.']);
        }
        $data = validator($data, ['catalog_item_id' => 'required|integer', 'unit_number' => 'nullable|integer|min:1', 'customer_name' => 'required|string|max:100', 'minutes' => 'required|integer|min:1|max:'.($reservation ? 10080 : 1440)])->validate();

        return DB::transaction(function () use ($data, $reservation) {
            $item = CatalogItem::lockForUpdate()->findOrFail($data['catalog_item_id']);
            if ($item->category !== 'Konsol') {
                throw ValidationException::withMessages(['catalog_item_id' => 'Pilih item kategori Konsol.']);
            }
            $unitNumber = $reservation?->unit_number ?? ($data['unit_number'] ?? null);
            $unitNumber ??= app(RentalAvailability::class)->chooseUnit($item, now(), now()->addMinutes((int) $data['minutes']), $reservation?->id);
            app(RentalAvailability::class)->ensure($item, now(), now()->addMinutes((int) $data['minutes']), $reservation?->id, null, (int) $unitNumber);
            $seconds = $data['minutes'] * 60;

            return PlaySession::create(['unit_number' => (int) $unitNumber, 'catalog_item_id' => $item->id, 'created_by' => auth()->id(), 'customer_name' => trim($data['customer_name']), 'total_seconds' => $seconds, 'remaining_seconds' => $seconds, 'hourly_rate' => $item->price, 'status' => 'running', 'ends_at' => now()->addSeconds($seconds)]);
        });
    }

    public function change(PlaySession $session, string $action, int $minutes = 0): PlaySession
    {
        $this->authorize();

        return DB::transaction(function () use ($session, $action, $minutes) {
            $item = CatalogItem::whereKey($session->catalog_item_id)->lockForUpdate()->firstOrFail();
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
                app(RentalAvailability::class)->ensure($item, now(), now()->addSeconds($remaining), null, $s->id, $s->unit_number);
                $s->status = 'running';
                $s->ends_at = now()->addSeconds($remaining);
            } elseif ($action === 'extend') {
                if ($minutes < 1 || $minutes > 1440 || $s->total_seconds + $minutes * 60 > 604800) {
                    throw ValidationException::withMessages(['minutes' => 'Tambahan waktu 1?1440 menit, total maksimal 7 hari.']);
                }
                app(RentalAvailability::class)->ensure($item, now(), now()->addSeconds($remaining + $minutes * 60), null, $s->id, $s->unit_number);
                $s->total_seconds += $minutes * 60;
                $s->remaining_seconds = $remaining + $minutes * 60;
                if ($s->status === 'running') {
                    $s->ends_at = now()->addSeconds($s->remaining_seconds);
                }
            }
            $s->save();
            $r = RentalReservation::where('play_session_id', $s->id)->lockForUpdate()->first();
            if ($r) {
                if ($action === 'finish') {
                    $r->status = 'completed';
                    $r->extensions()->where('status', 'pending')->update(['status' => 'rejected', 'decided_by' => auth()->id(), 'decided_at' => now()]);
                } elseif ($action === 'extend') {
                    $r->duration_minutes = (int) ($s->total_seconds / 60);
                    $r->total_price = (int) round($r->unit_rate * $r->duration_minutes / ($r->mode === 'daily' ? 1440 : 60));
                }
                if ($s->ends_at) {
                    $r->ends_at = $s->ends_at;
                }
                $r->save();
            }

            return $s;
        });
    }
}
