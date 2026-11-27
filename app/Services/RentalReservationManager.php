<?php

namespace App\Services;

use App\Models\CatalogItem;
use App\Models\RentalExtensionRequest;
use App\Models\RentalReservation;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RentalReservationManager
{
    public function __construct(private RentalAvailability $availability) {}

    private function admin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }

    public function quote(array $data): array
    {
        $data = validator($data, ['catalog_item_id' => 'required|integer|exists:catalog_items,id', 'mode' => 'required|in:lounge,daily', 'duration' => 'required|integer|min:1|max:480', 'starts_at' => 'required|date_format:Y-m-d\TH:i', 'unit_number' => 'nullable|integer|min:1', 'notes' => 'nullable|string|max:500', 'contact_name' => 'nullable|string|max:100', 'contact_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+() -]{8,30}$/']])->validate();
        $start = CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $data['starts_at'], 'Asia/Jakarta')->utc();
        if ($start < now()->subMinute() || $start > now()->addDays(30)) {
            throw ValidationException::withMessages(['starts_at' => __('Pilih jadwal mulai sekarang sampai 30 hari ke depan (WIB).')]);
        }
        if ($start < now()) {
            $start = CarbonImmutable::now();
        }
        $minutes = $data['mode'] === 'daily' ? (int) $data['duration'] * 1440 : (int) $data['duration'];
        if (($data['mode'] === 'daily' && $data['duration'] > 7) || ($data['mode'] === 'lounge' && ! in_array($minutes, [30, 60, 90, 120, 180, 240, 360, 480]))) {
            throw ValidationException::withMessages(['duration' => __('Pilih durasi yang tersedia. Harian maksimal 7 hari.')]);
        }
        $item = CatalogItem::findOrFail($data['catalog_item_id']);
        if (! $item->is_visible || $item->category !== 'Konsol' || ($data['mode'] === 'daily' && $item->daily_price === null)) {
            throw ValidationException::withMessages(['catalog_item_id' => __('Konsol atau tarif untuk mode sewa ini belum tersedia.')]);
        }
        if (isset($data['unit_number']) && $data['unit_number'] > $item->stock) {
            throw ValidationException::withMessages(['unit_number' => 'Nomor unit tidak tersedia untuk konsol ini.']);
        }
        $rate = $data['mode'] === 'daily' ? $item->daily_price : $item->price;

        return ['unit_number' => isset($data['unit_number']) ? (int) $data['unit_number'] : null, 'item' => $item, 'mode' => $data['mode'], 'starts_at' => $start, 'ends_at' => $start->addMinutes($minutes), 'duration_minutes' => $minutes, 'unit_rate' => $rate, 'total_price' => (int) round($rate * $minutes / ($data['mode'] === 'daily' ? 1440 : 60)), 'notes' => $data['notes'] ?? null, 'contact_name' => $data['contact_name'] ?? auth()->user()?->name, 'contact_phone' => $data['contact_phone'] ?? null];
    }

    public function create(array $data): RentalReservation
    {
        abort_unless(auth()->check(), 403);

        return DB::transaction(function () use ($data) {
            $q = $this->quote($data);
            $item = CatalogItem::lockForUpdate()->findOrFail($q['item']->id);
            $this->availability->ensure($item, $q['starts_at'], $q['ends_at'], null, null, $q['unit_number']);
            $duplicate = RentalReservation::where('user_id', auth()->id())->where('catalog_item_id', $item->id)->where('starts_at', $q['starts_at'])->where('duration_minutes', $q['duration_minutes'])->where('mode', $q['mode'])->where('unit_number', $q['unit_number'])->where('status', 'pending')->first();
            if ($duplicate) {
                return $duplicate;
            }
            if (RentalReservation::where('user_id', auth()->id())->where('status', 'pending')->count() >= 5) {
                throw ValidationException::withMessages(['reservation' => 'Maksimal 5 permintaan yang belum dikonfirmasi. Batalkan pesanan yang tidak diperlukan.']);
            }
            unset($q['item']);

            $reservation = RentalReservation::create($q + ['user_id' => auth()->id(), 'catalog_item_id' => $item->id, 'status' => 'pending']);
            app(AdminAlerts::class)->send('Reservasi baru', $reservation->reference().' • '.auth()->user()->name, url('/admin/rental-reservations'));

            return $reservation;
        }, 3);
    }

    public function adminAction(RentalReservation $record, string $action, ?string $note = null): RentalReservation
    {
        $this->admin();

        return DB::transaction(function () use ($record, $action, $note) {
            $item = CatalogItem::lockForUpdate()->findOrFail($record->catalog_item_id);
            $r = RentalReservation::lockForUpdate()->findOrFail($record->id);
            if (in_array($action, ['confirm', 'start', 'start_now']) && ! $r->paymentReady()) {
                throw ValidationException::withMessages(['payment' => 'Selesaikan pembayaran demo sebelum konfirmasi atau mulai sesi.']);
            }
            if ($action === 'confirm' && $r->status === 'confirmed') {
                return $r;
            }
            if ($action === 'confirm') {
                if ($r->status !== 'pending' || $r->ends_at <= now()) {
                    throw ValidationException::withMessages(['reservation' => 'Pesanan tidak dapat dikonfirmasi. Periksa status dan jadwalnya.']);
                }
                $r->unit_number ??= $this->availability->chooseUnit($item, $r->starts_at, $r->ends_at, $r->id);
                $this->availability->ensure($item, $r->starts_at, $r->ends_at, $r->id, null, $r->unit_number);
                $r->status = 'confirmed';
            } elseif ($action === 'reject') {
                if (! in_array($r->status, ['pending', 'confirmed'])) {
                    throw ValidationException::withMessages(['reservation' => 'Pesanan ini tidak dapat ditolak lagi.']);
                }
                $r->status = 'rejected';
                $r->admin_note = mb_substr($note ?? 'Jadwal belum dapat dipenuhi.', 0, 500);
            } elseif (in_array($action, ['start', 'start_now'])) {
                if ($r->status === 'playing') {
                    return $r;
                }
                if ($r->status !== 'confirmed' || ($action === 'start' && ($r->ends_at <= now() || $r->starts_at > now()->addMinutes(15)))) {
                    throw ValidationException::withMessages(['reservation' => 'Jadwal belum sesuai untuk mulai sesi. Gunakan Mulai sekarang untuk mengubah waktu mulai; unit tetap diperiksa agar tidak bentrok.']);
                }
                $session = app(PlaySessionManager::class)->start(['catalog_item_id' => $item->id, 'customer_name' => $r->user->name, 'minutes' => $r->duration_minutes], $r);
                $r->starts_at = $session->created_at;
                $r->play_session_id = $session->id;
                $r->unit_number = $session->unit_number;
                $r->status = 'playing';
                $r->ends_at = $session->ends_at;
            } else {
                throw ValidationException::withMessages(['reservation' => 'Aksi tidak dikenal.']);
            }
            $r->save();

            return $r;
        }, 3);
    }

    public function cancel(RentalReservation $record): void
    {
        abort_unless($record->user_id === auth()->id(), 403);
        DB::transaction(function () use ($record) {
            CatalogItem::whereKey($record->catalog_item_id)->lockForUpdate()->firstOrFail();
            $r = RentalReservation::lockForUpdate()->findOrFail($record->id);
            if (! in_array($r->status, ['pending', 'confirmed'])) {
                throw ValidationException::withMessages(['reservation' => 'Sesi yang sudah dimulai tidak dapat dibatalkan.']);
            }
            $r->update(['status' => 'cancelled']);
        });
    }

    public function requestExtension(RentalReservation $record, int $minutes): RentalExtensionRequest
    {
        abort_unless($record->user_id === auth()->id(), 403);

        return DB::transaction(function () use ($record, $minutes) {
            CatalogItem::whereKey($record->catalog_item_id)->lockForUpdate()->firstOrFail();
            $r = RentalReservation::lockForUpdate()->findOrFail($record->id);
            if ($r->status !== 'playing' || ! $r->playSession || $r->playSession->remaining() === 0 || ! in_array($minutes, [15, 30, 60, 120])) {
                throw ValidationException::withMessages(['minutes' => 'Perpanjangan hanya untuk sesi aktif, 15, 30, 60 atau 120 menit.']);
            }
            if ($r->extensions()->where('status', 'pending')->exists()) {
                throw ValidationException::withMessages(['minutes' => 'Permintaan sebelumnya masih menunggu admin.']);
            }

            $extension = $r->extensions()->create(['minutes' => $minutes, 'status' => 'pending']);
            app(AdminAlerts::class)->send('Permintaan tambah waktu', $r->reference().' • '.$minutes.' menit', url('/admin/rental-extension-requests'));

            return $extension;
        });
    }

    public function decideExtension(RentalExtensionRequest $request, bool $approve): void
    {
        $this->admin();
        DB::transaction(function () use ($request, $approve) {
            $parent = $request->rentalReservation;
            CatalogItem::whereKey($parent->catalog_item_id)->lockForUpdate()->firstOrFail();
            $r = RentalReservation::lockForUpdate()->findOrFail($parent->id);
            $e = RentalExtensionRequest::lockForUpdate()->findOrFail($request->id);
            if ($e->status !== 'pending') {
                return;
            }
            if ($approve) {
                if ($r->status !== 'playing' || ! $r->playSession) {
                    throw ValidationException::withMessages(['extension' => 'Sesi sudah tidak aktif.']);
                }
                app(PlaySessionManager::class)->change($r->playSession, 'extend', $e->minutes);
            }
            $e->update(['status' => $approve ? 'approved' : 'rejected', 'decided_by' => auth()->id(), 'decided_at' => now()]);
        }, 3);
    }
}
