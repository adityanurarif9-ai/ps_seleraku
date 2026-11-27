<?php

namespace Tests\Feature;

use App\Models\CatalogItem;
use App\Models\FoodOrder;
use App\Models\RentalReservation;
use App\Models\TestPayment;
use App\Models\User;
use App\Services\RentalReservationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestPaymentTest extends TestCase
{
    use RefreshDatabase;

    private function booking(): RentalReservation
    {
        $u = User::factory()->create();
        $this->actingAs($u);
        $item = CatalogItem::create(['name' => 'PS4', 'category' => 'Konsol', 'price' => 10000, 'daily_price' => 100000, 'stock' => 2]);

        return app(RentalReservationManager::class)->create(['catalog_item_id' => $item->id, 'mode' => 'lounge', 'duration' => 90, 'starts_at' => now()->addHour()->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i'), 'contact_name' => 'Adit', 'contact_phone' => '081234567890']);
    }

    public function test_checkout_uses_server_amount_and_success_is_only_an_idempotent_simulation(): void
    {
        $r = $this->booking();
        $this->post(route('payments.reservation', $r), ['amount' => 1])->assertStatus(303);
        $p = TestPayment::firstOrFail();
        $this->assertSame(15000, $p->amount);
        $this->post(route('payments.reservation', $r))->assertStatus(303);
        $this->assertSame(1, TestPayment::count());
        $this->get(route('payments.show', $p))->assertOk()->assertSee('SIMULASI')->assertSee('DANA');
        $this->post(route('payments.simulate', $p), ['method' => 'dana', 'result' => 'success'])->assertStatus(303);
        $this->assertSame('simulated_paid', $p->fresh()->status);
        $this->assertSame('pending', $r->fresh()->status);
        $this->post(route('payments.simulate', $p), ['method' => 'qris', 'result' => 'failed'])->assertStatus(303);
        $this->assertSame('dana', $p->fresh()->method);
        $this->assertSame('simulated_paid', $p->fresh()->status);
        $this->assertSame('Adit', $r->contact_name);
    }

    public function test_payment_is_private_and_rejects_cod_disabled_or_cancelled_simulations(): void
    {
        $r = $this->booking();
        $this->post(route('payments.reservation', $r));
        $p = TestPayment::firstOrFail();
        $this->post(route('payments.simulate', $p), ['method' => 'cod', 'result' => 'success'])->assertSessionHasErrors('method');
        config(['payments.demo_enabled' => false]);
        $this->post(route('payments.simulate', $p), ['method' => 'qris', 'result' => 'success'])->assertNotFound();
        config(['payments.demo_enabled' => true]);
        $r->update(['status' => 'cancelled']);
        $this->post(route('payments.simulate', $p), ['method' => 'qris', 'result' => 'success'])->assertStatus(422);
        $this->actingAs(User::factory()->create());
        $this->get(route('payments.show', $p))->assertForbidden();
        $this->post(route('payments.reservation', $r))->assertForbidden();
        $this->post(route('payments.simulate', $p), ['method' => 'qris', 'result' => 'success'])->assertForbidden();
    }

    public function test_food_payment_keeps_order_unconfirmed_and_can_retry_failed_test(): void
    {
        $u = User::factory()->create();
        $this->actingAs($u);
        $o = FoodOrder::create(['user_id' => $u->id, 'status' => 'pending', 'total_price' => 25000, 'request_key' => fake()->uuid()]);
        $this->post(route('payments.food', $o))->assertStatus(303);
        $p = TestPayment::firstOrFail();
        $this->assertSame(25000, $p->amount);
        $this->post(route('payments.simulate', $p), ['method' => 'qris', 'result' => 'failed'])->assertStatus(303);
        $this->assertSame('failed', $p->fresh()->status);
        $this->post(route('payments.simulate', $p), ['method' => 'qris', 'result' => 'success'])->assertStatus(303);
        $this->assertSame('simulated_paid',$p->fresh()->status);
        $this->assertSame('pending',$o->fresh()->status);
    }
}
