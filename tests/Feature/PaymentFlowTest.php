<?php

namespace Tests\Feature;

use App\Filament\Resources\Pages\ManageCatalogItems;
use App\Models\CatalogItem;
use App\Models\FoodOrder;
use App\Models\RentalReservation;
use App\Models\TestPayment;
use App\Models\User;
use App\Services\FoodOrderManager;
use App\Services\RentalReservationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    private function book(): array
    {
        $this->travelTo(now()->startOfMinute());
        config(['payments.demo_enabled' => true]);
        $owner = User::factory()->create(['name' => 'Private Owner Example']);
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($owner);
        $item = CatalogItem::create(['name' => 'PS4', 'category' => 'Konsol', 'stock' => 2, 'price' => 10000, 'daily_price' => 90000]);
        $this->post('/reservasi', ['catalog_item_id' => $item->id, 'unit_number' => 1, 'duration' => 90, 'mode' => 'lounge', 'starts_at' => now()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i')])->assertSessionHasNoErrors()->assertStatus(303);

        return [$owner, $admin, RentalReservation::firstOrFail(), TestPayment::firstOrFail()];
    }

    public function test_booking_then_phone_payment_then_admin_approval_then_timer(): void
    {
        [$owner,$admin,$r,$p] = $this->book();
        $this->assertSame('Menunggu pembayaran demo', $r->statusLabel());
        $this->actingAs($admin);
        try {
            app(RentalReservationManager::class)->adminAction($r, 'confirm');
            $this->fail('Unpaid confirmation must fail');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('payment', $e->errors());
        }
        $url = $p->demoScanUrl();
        auth()->logout();
        $this->get($url)->assertOk()->assertSee('Bayar demo sekarang')->assertDontSee('Private Owner Example')->assertDontSee($owner->email);
        $this->assertSame('pending', $p->fresh()->status);
        $this->post($url, ['amount' => 1, 'result' => 'failed', 'method' => 'dana'])->assertStatus(303);
        $this->assertSame('simulated_paid', $p->fresh()->status);
        $this->assertSame('qris', $p->fresh()->method);
        $this->assertSame(15000, $p->fresh()->amount);
        $this->assertSame('pending', $r->fresh()->status);
        $alerts = $admin->notifications()->count();
        $this->post($url)->assertStatus(303);
        $this->assertSame($alerts, $admin->notifications()->count());
        $this->actingAs($owner);
        $this->getJson(route('payments.status', $p))->assertOk()->assertJsonPath('status', 'simulated_paid')->assertJsonPath('next', route('reservations.show', $r));
        $this->assertSame('Menunggu konfirmasi', $r->fresh()->statusLabel());
        $this->actingAs($admin);
        $m = app(RentalReservationManager::class);
        $m->adminAction($r->fresh(), 'confirm');
        $m->adminAction($r->fresh(), 'start');
        $this->assertTrue($r->fresh()->timerRunning());
        $this->assertSame(5400, $r->fresh()->timerSeconds());
    }

    public function test_scan_link_is_signed_expires_and_owner_poll_remains_private(): void
    {
        [$owner,$admin,$r,$p] = $this->book();
        $url = $p->demoScanUrl();
        auth()->logout();
        $this->get($url.'&amount=1')->assertForbidden();
        $this->post($url.'&amount=1')->assertForbidden();
        $this->get(route('payments.scan', $p))->assertForbidden();
        $this->actingAs($admin);
        $this->getJson(route('payments.status', $p))->assertForbidden();
        auth()->logout();
        $this->travel(31)->minutes();
        $this->get($url)->assertForbidden();
        $this->post($url)->assertForbidden();
        $this->assertSame('pending', $p->fresh()->status);
    }

    public function test_cancelled_order_cannot_be_completed_from_old_qr_and_scan_post_requires_csrf(): void
    {
        [$owner,$admin,$r,$p] = $this->book();
        $url = $p->demoScanUrl();
        $r->update(['status' => 'cancelled']);
        auth()->logout();
        $this->get($url)->assertOk()->assertSee('Pesanan tidak aktif')->assertDontSee('Bayar demo sekarang');
        $this->post($url)->assertStatus(422);
        $this->assertSame('pending', $p->fresh()->status);
        $this->app->instance('env', 'production');
        $this->post($url)->assertStatus(419);
    }

    public function test_food_checkout_requires_payment_before_admin_preparation(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $food = CatalogItem::create(['name' => 'Teh', 'category' => 'Minuman', 'price' => 5000, 'stock' => 5]);
        $this->actingAs($owner)->withSession(['shopping_cart' => [$food->id => ['quantity' => 2, 'mode' => 'lounge']]]);
        $this->post('/pesanan-makanan', ['request_key' => (string) Str::uuid()])->assertStatus(303);
        $o = FoodOrder::firstOrFail();
        $p = TestPayment::firstOrFail();
        $this->assertSame(10000, $p->amount);
        $this->actingAs($admin);
        try {
            app(FoodOrderManager::class)->change($o, 'preparing');
            $this->fail('Unpaid food should not prepare');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('payment', $e->errors());
        }
        $url = $p->demoScanUrl();
        auth()->logout();
        $this->post($url)->assertStatus(303);
        $this->actingAs($admin);
        app(FoodOrderManager::class)->change($o->fresh(), 'preparing');
        $this->assertSame('preparing', $o->fresh()->status);
    }

    public function test_manual_demo_payment_redirects_to_waiting_order(): void
    {
        [$owner,$admin,$r,$p] = $this->book();
        $this->post(route('payments.simulate', $p), ['method' => 'dana', 'result' => 'failed'])->assertStatus(303);
        $this->assertFalse($r->fresh()->paymentReady());
        $this->post(route('payments.simulate', $p), ['method' => 'dana', 'result' => 'success'])->assertRedirect(route('reservations.show', $r));
        $this->assertTrue($r->fresh()->paymentReady());
    }

    public function test_admin_product_crud_updates_public_food_and_numbered_console_cards(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        Livewire::test(ManageCatalogItems::class)->callTableAction('create', data: ['name' => 'Menu admin baru', 'category' => 'Makanan', 'price' => 17000, 'stock' => 4, 'is_visible' => true])->assertHasNoTableActionErrors();
        $food = CatalogItem::where('name', 'Menu admin baru')->firstOrFail();
        $this->get('/menu-fnb')->assertSee('Menu admin baru')->assertSee('Rp 17.000');
        Livewire::test(ManageCatalogItems::class)->callTableAction('edit', $food, data: ['name' => 'Menu admin baru', 'category' => 'Makanan', 'price' => 19000, 'stock' => 4, 'is_visible' => true])->assertHasNoTableActionErrors();
        $this->get('/menu-fnb')->assertSee('Rp 19.000');
        Livewire::test(ManageCatalogItems::class)->callTableAction('addConsole', data: ['name' => 'PlayStation Admin', 'category' => 'Konsol', 'platform' => 'ps3', 'price' => 8000, 'daily_price' => 60000, 'stock' => 3, 'is_visible' => true])->assertHasNoTableActionErrors();
        $this->get('/page-ke2')->assertSee('PlayStation Admin · 01')->assertSee('PlayStation Admin · 03');
        Livewire::test(ManageCatalogItems::class)->callTableAction('delete',$food);
        $this->assertDatabaseMissing('catalog_items',['id' => $food->id]);
        $this->get('/menu-fnb')->assertDontSee('Menu admin baru');
    }
}
