<?php

namespace App\Services;

use App\Models\CatalogItem;
use App\Models\PlaySession;
use App\Models\RentalReservation;
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;

class RentalAvailability
{
    // Endpoints are half-open: a rental ending at 11:00 permits another at 11:00.
    public function available(CatalogItem $item, CarbonInterface $from, CarbonInterface $to, ?int $excludeReservation = null, ?int $excludeSession = null): int
    {
        if ($item->category !== 'Konsol' || $to <= $from) {
            return 0;
        }
        $events = [];
        $add = function ($start, $end) use (&$events, $from, $to) {
            $a = max($start->getTimestamp(), $from->getTimestamp());
            $b = min($end->getTimestamp(), $to->getTimestamp());
            if ($a < $b) {
                $events[$a] = ($events[$a] ?? 0) + 1;
                $events[$b] = ($events[$b] ?? 0) - 1;
            }
        };
        $reserved = RentalReservation::where('catalog_item_id', $item->id)->where('status', 'confirmed')->where('starts_at', '<', $to)->where('ends_at', '>', $from);
        if ($excludeReservation) {
            $reserved->whereKeyNot($excludeReservation);
        }
        foreach ($reserved->get() as $r) {
            $add($r->starts_at, $r->ends_at);
        }
        $sessions = PlaySession::where('catalog_item_id', $item->id)->where(function ($q) use ($from) {
            $q->where('status', 'paused')->orWhere(fn ($r) => $r->where('status', 'running')->where('ends_at', '>', $from));
        });
        if ($excludeSession) {
            $sessions->whereKeyNot($excludeSession);
        }
        foreach ($sessions->get() as $s) {
            $add($s->created_at, $s->status === 'paused' ? $to : $s->ends_at);
        }
        ksort($events, SORT_NUMERIC);
        $current = 0;
        $peak = 0;
        foreach ($events as $delta) {
            $current += $delta;
            $peak = max($peak, $current);
        }

        return max(0, $item->stock - $peak);
    }

    public function ensure(CatalogItem $item, CarbonInterface $from, CarbonInterface $to, ?int $reservation = null, ?int $session = null): void
    {
        if ($this->available($item, $from, $to, $reservation, $session) < 1) {
            throw ValidationException::withMessages(['catalog_item_id' => 'Unit tidak tersedia pada rentang waktu ini. Pilih jadwal lain.']);
        }
    }
}
