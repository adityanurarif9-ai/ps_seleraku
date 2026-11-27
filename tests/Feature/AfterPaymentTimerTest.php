<?php

namespace Tests\Feature;

use App\Models\CatalogItem;
use App\Models\RentalReservation;
use App\Models\TestPayment;
use App\Models\User;
use App\Services\PlaySessionManager;
use App\Services\RentalReservationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AfterPaymentTimerTest extends TestCase
{
    use RefreshDatabase;

    private function order(): array
    {
        $this->travelTo(now()->startOfMinute());
        $this->actingAs(User::factory()->create());
        $item = CatalogItem::create(['name' => 'PS4 timer', 'category' => 'Konsol', 'price' => 10000, 'stock' => 2]);
        $this->post('/reservasi', ['catalog_item_id' => $item->id, 'mode' => 'lounge', 'duration' => 90, 'starts_at' => now()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i')])->assertSessionHasNoErrors();

        return [RentalReservation::firstOrFail(), TestPayment::firstOrFail()];
    }

    public function test_same_phone_qr_payment_goes_directly_to_timer_and_cannot_get_stuck_on_paid_checkout(): void
    {
        [$r,$p] = $this->order();
        $url = $p->demoScanUrl();
        $this->post($url)->assertStatus(303)->assertRedirect(route('reservations.show', $r));
        $page = $this->get(route('reservations.show', $r))->assertOk()->assertSeeInOrder(['id="rs-timer"', '01:30:00', 'Langkah pesanan', 'Rincian reservasi'], false)->assertSee('Durasi belum berkurang.')->assertDontSee('data-rs-pay', false);
        $this->get(route('payments.show', $p))->assertRedirect(route('reservations.show', $r));
        $this->get($url)->assertRedirect(route('reservations.show', $r));
        $this->assertFalse($r->fresh()->timerRunning());
        $this->getJson(route('reservations.status', $r))->assertJsonPath('payment_ready', true)->assertJsonPath('remaining', 5400)->assertJsonPath('timer_title', 'Durasi yang kamu pesan');
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $manager = app(RentalReservationManager::class);
        $manager->adminAction($r, 'confirm');
        $manager->adminAction($r->fresh(), 'start');
        $this->travel(1)->seconds();
        $this->getJson(route('reservations.status', $r))->assertJsonPath('remaining', 5399)->assertJsonPath('running', true)->assertJsonPath('timer_title', 'Sisa waktu sewa');
        app(PlaySessionManager::class)->change($r->fresh()->playSession, 'pause');
        $this->travel(60)->seconds();
        $this->getJson(route('reservations.status', $r))->assertJsonPath('remaining', 5399)->assertJsonPath('running', false);
    }

    public function test_anonymous_scan_success_has_a_clear_link_back_to_the_protected_timer(): void
    {
        [$r,$p] = $this->order();
        $url = $p->demoScanUrl();
        auth()->logout();
        $this->post($url)->assertRedirect($url);
        $this->get($url)->assertOk()->assertSee('Lihat timer &amp; pesanan', false)->assertSee(route('reservations.show', $r), false);
        $this->get(route('reservations.show', $r))->assertRedirect(route('login'));
    }
}
