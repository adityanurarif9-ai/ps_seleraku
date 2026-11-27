<?php

namespace App\Http\Controllers;

use App\Models\FoodOrder;
use App\Models\RentalReservation;
use App\Models\TestPayment;
use App\Services\AdminAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TestPaymentController extends Controller
{
    public function show(TestPayment $payment)
    {
        abort_unless($payment->user_id === auth()->id(), 403);
        $scanUrl = $payment->demoScanUrl();
        $record = $payment->rentalReservation ?? $payment->foodOrder;
        $active = $record && ! in_array($record->status, ['cancelled', 'rejected', 'completed']);

        return response()->view('rentals.payment', compact('payment', 'scanUrl', 'active'))->header('Cache-Control', 'no-store, private');
    }

    public function status(TestPayment $payment)
    {
        abort_unless($payment->user_id === auth()->id(), 403);

        return response()->json(['status' => $payment->status, 'next' => $this->nextUrl($payment)])->header('Cache-Control', 'no-store, private');
    }

    public function scan(Request $request, TestPayment $payment)
    {
        abort_unless(config('payments.demo_enabled'), 404);
        $record = $payment->rentalReservation ?? $payment->foodOrder;
        $active = $record && ! in_array($record->status, ['cancelled', 'rejected', 'completed']);

        return response()->view('rentals.scan-pay', ['payment' => $payment, 'active' => $active, 'action' => $request->fullUrl()])->header('Cache-Control', 'no-store, private')->header('Referrer-Policy', 'no-referrer')->header('X-Robots-Tag', 'noindex, nofollow');
    }

    public function scanPay(Request $request, TestPayment $payment)
    {
        abort_unless(config('payments.demo_enabled'), 404);
        // The signed URL grants only the ability to complete this simulated payment.
        // GET never changes state, and a session CSRF token is still required for POST.
        $this->complete($payment, 'qris', 'success');

        return redirect($request->fullUrl(), 303);
    }

    public function reservation(RentalReservation $reservation)
    {
        return $this->start($reservation, 'rental_reservation_id');
    }

    public function food(FoodOrder $order)
    {
        return $this->start($order, 'food_order_id');
    }

    private function start($record, string $key)
    {
        abort_unless($record->user_id === auth()->id(), 403);
        abort_unless(config('payments.demo_enabled'), 503, 'Pembayaran belum diaktifkan pengelola.');
        $p = DB::transaction(function () use ($record, $key) {
            $r = $record->newQuery()->lockForUpdate()->findOrFail($record->id);
            abort_if(in_array($r->status, ['cancelled', 'rejected', 'completed']), 422);

            return TestPayment::firstOrCreate([$key => $r->id], ['user_id' => auth()->id(), 'amount' => $r->total_price, 'status' => 'pending']);
        }, 3);

        return redirect()->route('payments.show', $p, status: 303);
    }

    public function simulate(Request $request, TestPayment $payment)
    {
        abort_unless($payment->user_id === auth()->id(), 403);
        abort_unless(config('payments.demo_enabled'), 404);
        $data = $request->validate(['method' => ['required', Rule::in(array_keys(TestPayment::METHODS))], 'result' => 'required|in:success,failed']);
        $this->complete($payment, $data['method'], $data['result']);

        return $data['result'] === 'success' ? redirect($this->nextUrl($payment), 303)->with('success', __('Pembayaran demo berhasil. Menunggu konfirmasi admin.')) : redirect()->route('payments.show', $payment, status: 303);
    }

    private function nextUrl(TestPayment $payment): string
    {
        return $payment->rental_reservation_id ? route('reservations.show', $payment->rental_reservation_id) : route('food-orders.index');
    }

    private function complete(TestPayment $payment, string $method, string $result): void
    {
        DB::transaction(function () use ($payment, $method, $result) {
            // Same lock order as payment creation: order, then payment.
            $record = $payment->rental_reservation_id ? RentalReservation::lockForUpdate()->findOrFail($payment->rental_reservation_id) : FoodOrder::lockForUpdate()->findOrFail($payment->food_order_id);
            $p = TestPayment::lockForUpdate()->findOrFail($payment->id);
            if ($p->status === 'simulated_paid') {
                return;
            }
            abort_if(in_array($record->status, ['cancelled', 'rejected', 'completed']), 422);
            $p->update(['method' => $method, 'status' => $result === 'success' ? 'simulated_paid' : 'failed', 'simulated_at' => now()]);
            if ($result === 'success') {
                app(AdminAlerts::class)->send('Pembayaran demo selesai', $record->reference().' — siap diperiksa', url($p->rental_reservation_id ? '/admin/rental-reservations' : '/admin/food-orders'));
            }
        }, 3);
    }
}
