<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\RentalReservation;
use App\Services\RentalAvailability;
use App\Services\RentalReservationManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RentalReservationController extends Controller
{
    public function index(Request $request)
    {
        $history = $request->query('tab') === 'history';
        $q = RentalReservation::where('user_id', $request->user()->id)->with(['catalogItem', 'playSession']);
        if ($history) {
            $q->whereIn('status', ['completed', 'cancelled', 'rejected']);
        } else {
            $q->whereIn('status', ['pending', 'confirmed', 'playing']);
        }

        return view('rentals.index', ['rentals' => $q->latest()->paginate(12)->withQueryString(), 'history' => $history]);
    }

    public function create(Request $request)
    {
        $items = CatalogItem::where('category', 'Konsol')->where('is_visible', true)->orderBy('name')->get();
        $platform = $request->query('platform', '');
        $selected = $items->firstWhere('id', (int) $request->query('catalog_item_id')) ?? $items->first(fn ($i) => match ($platform) {
            'ps5' => str_contains($i->name, '5'),'ps4' => str_contains($i->name, '4'),'ps3' => str_contains($i->name, '3'),'nintendo' => str_contains(strtolower($i->name), 'nintendo'),default => false
        });

        return view('rentals.create', compact('items', 'selected'));
    }

    public function availability(Request $request, RentalReservationManager $manager, RentalAvailability $availability)
    {
        $q = $manager->quote($request->only(['catalog_item_id', 'mode', 'duration', 'starts_at', 'notes']));

        return response()->json(['available' => $availability->available($q['item'], $q['starts_at'], $q['ends_at']), 'total_price' => $q['total_price'], 'ends_at' => $q['ends_at']->setTimezone('Asia/Jakarta')->format('d M Y, H:i').' WIB', 'duration_minutes' => $q['duration_minutes']])->header('Cache-Control', 'no-store, private');
    }

    public function store(Request $request, RentalReservationManager $manager)
    {
        $r = $manager->create($request->only(['catalog_item_id', 'mode', 'duration', 'starts_at', 'notes']));

        return redirect()->route('reservations.show', $r)->with('success', 'Reservasi tersimpan. Tunggu konfirmasi admin sebelum datang.');
    }

    public function show(RentalReservation $reservation)
    {
        Gate::authorize('view', $reservation);
        $reservation->load(['catalogItem', 'playSession', 'extensions']);

        return view('rentals.show', compact('reservation'));
    }

    public function status(RentalReservation $reservation)
    {
        Gate::authorize('view', $reservation);
        $reservation->load(['playSession', 'extensions']);

        return response()->json(['status' => $reservation->status, 'label' => $reservation->statusLabel(), 'remaining' => $reservation->playSession?->remaining() ?? 0, 'running' => $reservation->status === 'playing' && $reservation->playSession?->status === 'running', 'price' => $reservation->priceLabel(), 'duration_minutes' => $reservation->duration_minutes, 'ends_at' => $reservation->ends_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i').' WIB', 'extension_pending' => $reservation->extensions->contains('status', 'pending'), 'extensions' => $reservation->extensions->map(fn ($e) => ['id' => $e->id, 'minutes' => $e->minutes, 'status' => $e->status])->values()])->header('Cache-Control', 'no-store, private');
    }

    public function cancel(RentalReservation $reservation, RentalReservationManager $manager)
    {
        $manager->cancel($reservation);

        return back()->with('success', 'Reservasi dibatalkan.');
    }

    public function extend(Request $request, RentalReservation $reservation, RentalReservationManager $manager)
    {
        $data = $request->validate(['minutes' => 'required|integer|in:15,30,60,120']);
        $manager->requestExtension($reservation, (int) $data['minutes']);

        return back()->with('success', 'Permintaan tambahan waktu dikirim ke admin. Timer berubah setelah disetujui.');
    }
}
