<?php

namespace Tests\Feature;

use App\Filament\Resources\Pages\ManageRentalExtensionRequests;
use App\Filament\Resources\Pages\ManageRentalReservations;
use App\Models\CatalogItem;
use App\Models\RentalExtensionRequest;
use App\Models\RentalReservation;
use App\Models\User;
use App\Services\PlaySessionManager;
use App\Services\RentalAvailability;
use App\Services\RentalReservationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class RentalReservationTest extends TestCase
{
    use RefreshDatabase;

    protected User $player;

    protected User $admin;

    protected CatalogItem $item;

    protected function setUp(): void
    {
        parent::setUp();
        $this->freezeTime();
        $this->player = User::factory()->create(['is_admin' => false]);
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->item = CatalogItem::create(['name' => 'PlayStation 5', 'category' => 'Konsol', 'price' => 15000, 'daily_price' => 150000, 'stock' => 1]);
        $this->actingAs($this->player);
    }

    private function data(int $inMinutes = 60, int $duration = 60): array
    {
        return ['catalog_item_id' => $this->item->id, 'mode' => 'lounge', 'duration' => $duration, 'starts_at' => now()->addMinutes($inMinutes)->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i'), 'notes' => 'Main bersama teman'];
    }

    private function reserve(int $inMinutes = 60, int $duration = 60): RentalReservation
    {
        return app(RentalReservationManager::class)->create($this->data($inMinutes, $duration));
    }

    private function active(): RentalReservation
    {
        $r = $this->reserve(0);
        $this->actingAs($this->admin);
        $manager = app(RentalReservationManager::class);
        $manager->adminAction($r, 'confirm');

        return $manager->adminAction($r, 'start')->fresh();
    }

    public function test_guest_routes_require_login_and_player_pages_render(): void
    {
        auth()->logout();
        $this->get('/rental-saya')->assertRedirect(route('login'));
        $this->get('/reservasi/baru')->assertRedirect(route('login'));
        $this->actingAs($this->player);
        $this->get('/reservasi/baru')->assertOk()->assertSee('Reservasi konsol');
        $this->get('/rental-saya')->assertOk()->assertSee('Belum ada reservasi aktif');
    }

    public function test_request_is_saved_for_owner_with_server_price_and_is_idempotent(): void
    {
        $data = $this->data() + ['user_id' => $this->admin->id, 'total_price' => 1, 'status' => 'confirmed'];
        $this->post('/reservasi', $data)->assertSessionHasNoErrors()->assertRedirect();
        $r = RentalReservation::firstOrFail();
        $this->assertSame($this->player->id, $r->user_id);
        $this->assertSame(15000, $r->total_price);
        $this->assertSame('pending', $r->status);
        $this->post('/reservasi', $data)->assertSessionHasNoErrors();
        $this->assertSame(1, RentalReservation::count());
        $this->get(route('reservations.show', $r))->assertOk()->assertSee('Menunggu konfirmasi');
    }

    public function test_daily_quote_and_invalid_catalog_or_dates(): void
    {
        $d = $this->data();
        $d['mode'] = 'daily';
        $d['duration'] = 2;
        $this->getJson(route('reservations.availability', $d))->assertOk()->assertJsonPath('total_price', 300000)->assertJsonPath('duration_minutes', 2880)->assertJsonPath('available', 1);
        $this->post('/reservasi', array_replace($d, ['starts_at' => now()->subDay()->format('Y-m-d\TH:i')]))->assertSessionHasErrors('starts_at');
        $food = CatalogItem::create(['name' => 'Teh', 'category' => 'Minuman', 'stock' => 10, 'price' => 5000]);
        $this->post('/reservasi', array_replace($this->data(), ['catalog_item_id' => $food->id]))->assertSessionHasErrors('catalog_item_id');
    }

    public function test_other_players_cannot_read_status_cancel_or_extend_someones_rental(): void
    {
        $r = $this->reserve();
        $other = User::factory()->create(['is_admin' => false]);
        $this->actingAs($other);
        $this->get(route('reservations.show', $r))->assertForbidden();
        $this->getJson(route('reservations.status', $r))->assertForbidden();
        $this->post(route('reservations.cancel', $r))->assertForbidden();
        $this->post(route('reservations.extend', $r), ['minutes' => 30])->assertForbidden();
        $this->assertSame('pending', $r->fresh()->status);
    }

    public function test_admin_confirmation_rechecks_capacity_and_player_cannot_confirm(): void
    {
        $first = $this->reserve();
        $other = User::factory()->create(['is_admin' => false]);
        $this->actingAs($other);
        $second = $this->reserve();
        $this->actingAs($this->admin);
        app(RentalReservationManager::class)->adminAction($first, 'confirm');
        try {
            app(RentalReservationManager::class)->adminAction($second, 'confirm');
            $this->fail('Must reject overlapping capacity.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('catalog_item_id', $e->errors());
        }
        $this->actingAs($this->player);
        $this->get('/admin/rental-reservations')->assertRedirect(url('/admin/login'));
        $this->assertSame('pending', $second->fresh()->status);
    }

    public function test_availability_handles_back_to_back_bookings_and_multi_unit_peak(): void
    {
        $this->item->update(['stock' => 2]);
        $r1 = $this->reserve(60, 60);
        $r2 = $this->reserve(120, 60);
        $this->actingAs($this->admin);
        $m = app(RentalReservationManager::class);
        $r1 = $m->adminAction($r1, 'confirm');
        $r2 = $m->adminAction($r2, 'confirm');
        $this->assertSame(1, app(RentalAvailability::class)->available($this->item, $r1->starts_at, $r2->ends_at));
        $this->assertSame(2, app(RentalAvailability::class)->available($this->item, $r2->ends_at, $r2->ends_at->addHour()));
    }

    public function test_linked_timer_pause_resume_and_finish_sync_to_owner(): void
    {
        $r = $this->active();
        $this->travel(10)->minutes();
        app(PlaySessionManager::class)->change($r->playSession, 'pause');
        $this->actingAs($this->player);
        $this->getJson(route('reservations.status', $r))->assertOk()->assertJsonPath('remaining', 3000)->assertJsonPath('label', 'Sedang dijeda')->assertJsonPath('running', false);
        $this->travel(20)->minutes();
        $this->getJson(route('reservations.status', $r))->assertJsonPath('remaining', 3000);
        $this->actingAs($this->admin);
        app(PlaySessionManager::class)->change($r->playSession, 'resume');
        $this->travel(5)->minutes();
        $this->actingAs($this->player);
        $this->getJson(route('reservations.status', $r))->assertJsonPath('remaining', 2700);
        $this->actingAs($this->admin);
        app(PlaySessionManager::class)->change($r->playSession, 'finish');
        $this->assertSame('completed', $r->fresh()->status);
        $this->actingAs($this->player);
        $this->get('/rental-saya?tab=history')->assertOk()->assertSee($r->reference());
    }

    public function test_extension_requires_approval_applies_once_and_keeps_cost_snapshot(): void
    {
        $r = $this->active();
        $this->actingAs($this->player);
        $this->post(route('reservations.extend', $r), ['minutes' => 30])->assertSessionHasNoErrors();
        $this->post(route('reservations.extend', $r), ['minutes' => 30])->assertSessionHasErrors('minutes');
        $e = RentalExtensionRequest::firstOrFail();
        $this->assertSame(3600, $r->playSession->fresh()->remaining());
        $this->item->update(['price' => 90000]);
        $this->actingAs($this->admin);
        $m = app(RentalReservationManager::class);
        $m->decideExtension($e, true);
        $m->decideExtension($e, true);
        $this->assertSame(5400, $r->playSession->fresh()->remaining());
        $this->assertSame(22500, $r->fresh()->total_price);
        $this->assertSame('approved', $e->fresh()->status);
    }

    public function test_conflicting_extension_rolls_back_approval_timer_and_price(): void
    {
        $r = $this->active();
        $this->actingAs($this->player);
        $next = $this->reserve(61, 60);
        $e = app(RentalReservationManager::class)->requestExtension($r, 30);
        $this->actingAs($this->admin);
        $m = app(RentalReservationManager::class);
        $m->adminAction($next, 'confirm');
        try {
            $m->decideExtension($e, true);
            $this->fail('Must reject overlapping extension.');
        } catch (ValidationException $ex) {
            $this->assertArrayHasKey('catalog_item_id', $ex->errors());
        }
        $this->assertSame('pending', $e->fresh()->status);
        $this->assertSame(3600, $r->playSession->fresh()->remaining());
        $this->assertSame(15000, $r->fresh()->total_price);
    }

    public function test_manual_admin_session_also_respects_confirmed_reservations(): void
    {
        $r = $this->reserve(30);
        $this->actingAs($this->admin);
        app(RentalReservationManager::class)->adminAction($r, 'confirm');
        $this->expectException(ValidationException::class);
        app(PlaySessionManager::class)->start(['catalog_item_id' => $this->item->id, 'customer_name' => 'Walk in', 'minutes' => 60]);
    }

    public function test_cancel_releases_slot_but_not_after_session_started(): void
    {
        $r = $this->reserve();
        $this->actingAs($this->admin);
        app(RentalReservationManager::class)->adminAction($r, 'confirm');
        $this->actingAs($this->player);
        $this->post(route('reservations.cancel', $r))->assertSessionHasNoErrors();
        $this->assertSame('cancelled', $r->fresh()->status);
        $active = $this->active();
        $this->actingAs($this->player);
        $this->post(route('reservations.cancel', $active))->assertSessionHasErrors('reservation');
    }

    public function test_filament_actions_confirm_start_and_approve_extension(): void
    {
        $r = $this->reserve(0);
        $this->actingAs($this->admin);
        Livewire::test(ManageRentalReservations::class)->callTableAction('confirm', $r)->assertHasNoTableActionErrors();
        Livewire::test(ManageRentalReservations::class)->callTableAction('start', $r)->assertHasNoTableActionErrors();
        $this->assertSame('playing', $r->fresh()->status);
        $this->actingAs($this->player);
        $e = app(RentalReservationManager::class)->requestExtension($r->fresh(), 15);
        $this->actingAs($this->admin);
        Livewire::test(ManageRentalExtensionRequests::class)->callTableAction('approve', $e)->assertHasNoTableActionErrors();
        $this->assertSame('approved', $e->fresh()->status);
    }

    public function test_browser_form_string_values_work_for_hourly_availability_and_submission(): void
    {
        $data = array_map(fn ($value) => (string) $value, $this->data());
        $this->getJson(route('reservations.availability', $data))->assertOk()->assertJsonPath('total_price', 15000)->assertJsonPath('available', 1);
        $this->post(route('reservations.store'), $data)->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(60, RentalReservation::firstOrFail()->duration_minutes);
    }

    public function test_availability_polling_does_not_consume_booking_rate_limit(): void
    {
        $data = $this->data();
        for ($i = 0; $i < 12; $i++) {
            $this->getJson(route('reservations.availability', $data))->assertOk();
        }
        $this->post(route('reservations.store'), $data)->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseCount('rental_reservations', 1);
    }

    public function test_ninety_minute_clock_previews_full_duration_then_counts_down_only_after_start(): void
    {
        $r = $this->reserve(60, 90);
        $this->get(route('reservations.show', $r))->assertOk()->assertSee('01:30:00');
        $this->getJson(route('reservations.status', $r))->assertJsonPath('remaining', 5400)->assertJsonPath('running', false);
        $this->travel(5)->minutes();
        $this->getJson(route('reservations.status', $r))->assertJsonPath('remaining', 5400)->assertJsonPath('running', false);
        $this->actingAs($this->admin);
        $m = app(RentalReservationManager::class);
        $m->adminAction($r, 'confirm');
        $this->getJson(route('reservations.status', $r))->assertJsonPath('remaining', 5400)->assertJsonPath('running', false);
        $r = $m->adminAction($r, 'start_now')->fresh();
        $this->getJson(route('reservations.status', $r))->assertJsonPath('remaining', 5400)->assertJsonPath('running', true);
        $this->travel(1)->minutes();
        $this->getJson(route('reservations.status', $r))->assertJsonPath('remaining', 5340);
        app(PlaySessionManager::class)->change($r->playSession, 'pause');
        $this->travel(10)->minutes();
        $this->getJson(route('reservations.status', $r))->assertJsonPath('remaining', 5340)->assertJsonPath('running', false);
        $this->get(route('reservations.show', $r))->assertSee('01:29:00');
        app(PlaySessionManager::class)->change($r->playSession, 'finish');
        $this->getJson(route('reservations.status', $r))->assertJsonPath('remaining', 0)->assertJsonPath('running', false);
    }
}
