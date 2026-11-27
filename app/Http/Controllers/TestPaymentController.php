<?php

namespace App\Http\Controllers;

use App\Models\FoodOrder;
use App\Models\RentalReservation;
use App\Models\TestPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TestPaymentController extends Controller
{
    public function show(TestPayment $payment)
    {
        abort_unless($payment->user_id === auth()->id(), 403);

        return response()->view('rentals.payment', compact('payment'))->header('Cache-Control', 'no-store, private');
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
        abort_if(in_array($record->status, ['cancelled', 'rejected', 'completed']), 422);
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
        DB::transaction(function () use ($payment, $data) {
            $p = TestPayment::lockForUpdate()->findOrFail($payment->id);
            $record = $p->rentalReservation ?? $p->foodOrder;
            abort_if(! $record || in_array($record->status, ['cancelled', 'rejected', 'completed']), 422);
            if ($p->status === 'simulated_paid') {
                return;
            }
            $p->update(['method' => $data['method'], 'status' => $data['result'] === 'success' ? 'simulated_paid' : 'failed', 'simulated_at' => now()]);
        }, 3);

        return redirect()->route('payments.show',$payment,status: 303);
    }
}
