<?php

namespace App\Http\Controllers;

use App\Models\FoodOrder;
use App\Models\RentalReservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;

class ReceiptController extends Controller
{
    public function rental(RentalReservation $reservation)
    {
        Gate::authorize('view', $reservation);
        $reservation->load(['user', 'catalogItem', 'testPayment']);
        $unit = $reservation->unit_number ? ' / Unit '.str_pad((string) $reservation->unit_number, 2, '0', STR_PAD_LEFT) : '';
        $data = ['reference' => $reservation->reference(), 'kind' => __('Rental konsol'), 'customer' => $reservation->contact_name ?: $reservation->user->name, 'orderedAt' => $reservation->created_at, 'status' => $reservation->statusLabel(), 'payment' => $reservation->testPayment, 'total' => $reservation->total_price,
            'lines' => [['name' => $reservation->catalogItem->name.$unit, 'detail' => $reservation->duration_minutes.' '.__('menit').' / '.($reservation->mode === 'daily' ? __('Bawa pulang') : __('Main di tempat')), 'quantity' => 1, 'unit_price' => $reservation->total_price]],
            'schedule' => $reservation->starts_at->timezone('Asia/Jakarta')->format('d M Y, H:i').' - '.$reservation->ends_at->timezone('Asia/Jakarta')->format('d M Y, H:i').' WIB', 'notes' => $reservation->notes];

        return $this->download($data);
    }

    public function food(FoodOrder $order)
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()->is_admin, 403);
        $order->load(['user', 'items', 'testPayment']);

        return $this->download(['reference' => $order->reference(), 'kind' => __('Pesanan Makanan'), 'customer' => $order->user->name, 'orderedAt' => $order->created_at, 'status' => $order->statusLabel(), 'payment' => $order->testPayment, 'total' => $order->total_price, 'schedule' => null, 'notes' => $order->notes,
            'lines' => $order->items->map(fn ($line) => ['name' => $line->name, 'detail' => '', 'quantity' => $line->quantity, 'unit_price' => $line->unit_price])->all()]);
    }

    private function download(array $data)
    {
        // No external resources, scripts, or user-supplied file paths in PDF rendering.
        $pdf = Pdf::loadView('receipts.order', $data)->setPaper('a4')->setOptions(['defaultFont' => 'DejaVu Sans', 'isRemoteEnabled' => false, 'isPhpEnabled' => false, 'isJavascriptEnabled' => false, 'tempDir' => sys_get_temp_dir(), 'fontCache' => sys_get_temp_dir(), 'chroot' => public_path()]);

        return $pdf->download('Struk-DEMO-'.$data['reference'].'.pdf')->header('Cache-Control', 'private, no-store, max-age=0')->header('X-Content-Type-Options', 'nosniff');
    }
}
