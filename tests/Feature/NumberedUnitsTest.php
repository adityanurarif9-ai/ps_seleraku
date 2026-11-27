<?php

namespace Tests\Feature;

use App\Models\CatalogItem;
use App\Models\User;
use App\Services\PlaySessionManager;
use App\Services\RentalReservationManager;
use App\Services\ShoppingCart;
use App\Services\WebsiteCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class NumberedUnitsTest extends TestCase
{
    use RefreshDatabase;

    private function setupItem(): CatalogItem
    {
        $this->travelTo(now()->startOfMinute());
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        return CatalogItem::create(['name' => 'PlayStation 4', 'category' => 'Konsol', 'platform' => 'ps4', 'stock' => 2, 'price' => 10000, 'daily_price' => 90000]);
    }

    private function data(CatalogItem $i, int $unit, int $offset = 0): array
    {
        return ['catalog_item_id' => $i->id, 'unit_number' => $unit, 'mode' => 'lounge', 'duration' => 60, 'starts_at' => now()->addMinutes($offset)->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i')];
    }

    public function test_each_card_has_a_unique_number_and_retains_real_stock(): void
    {
        $i = $this->setupItem();
        $cards = app(WebsiteCatalog::class)->data()['proUnits'];
        $this->assertCount(2, $cards);
        $this->assertSame('PlayStation 4 · 01', $cards[0]['name']);
        $this->assertSame('catalog-'.$i->id.'-unit-2', $cards[1]['id']);
        $this->assertSame(2, $i->fresh()->stock);
        $this->get('/reservasi/baru?catalog_item_id='.$i->id.'&unit_number=2')->assertOk()->assertSee('value="2" selected', false);
    }

    public function test_two_different_units_start_together_and_same_unit_cannot_be_double_booked(): void
    {
        $i = $this->setupItem();
        $m = app(RentalReservationManager::class);
        $a = $m->create($this->data($i, 1));
        $b = $m->create($this->data($i, 2));
        $m->adminAction($a, 'confirm');
        $m->adminAction($b, 'confirm');
        $sa = $m->adminAction($a->fresh(), 'start')->playSession;
        $sb = $m->adminAction($b->fresh(), 'start')->playSession;
        $this->assertSame(1, $sa->unit_number);
        $this->assertSame(2, $sb->unit_number);
        $this->getJson(route('reservations.availability', $this->data($i, 1)))->assertJsonPath('available', 0);
        $this->expectException(ValidationException::class);
        $m->create($this->data($i, 1));
    }

    public function test_selected_number_is_validated_and_confirmation_cannot_steal_another_unit(): void
    {
        $i = $this->setupItem();
        $m = app(RentalReservationManager::class);
        $a = $m->create($this->data($i, 1));
        $b = $m->create($this->data($i, 1));
        $this->assertSame($a->id, $b->id);
        $this->post('/reservasi', $this->data($i, 3))->assertSessionHasErrors('unit_number');
        $other = User::factory()->create();
        $this->actingAs($other);
        $b = $m->create($this->data($i, 1));
        $this->actingAs($a->user);
        $m->adminAction($a, 'confirm');
        $this->expectException(ValidationException::class);
        $m->adminAction($b, 'confirm');
    }

    public function test_pause_and_extension_keep_same_unit_and_conflicting_extension_rolls_back(): void
    {
        $i = $this->setupItem();
        $m = app(RentalReservationManager::class);
        $r = $m->create($this->data($i, 2));
        $m->adminAction($r, 'confirm');
        $r = $m->adminAction($r->fresh(), 'start');
        $session = $r->playSession;
        $p = app(PlaySessionManager::class);
        $p->change($session, 'pause');
        $p->change($session->fresh(), 'resume');
        $this->assertSame(2, $session->fresh()->unit_number);
        $next = $m->create($this->data($i, 2, 60));
        $m->adminAction($next, 'confirm');
        try {
            $p->change($session->fresh(), 'extend', 30);
            $this->fail('Overlap must be rejected');
        } catch (ValidationException) {
            $this->assertSame(3600, $session->fresh()->total_seconds);
        }
        $this->expectException(ValidationException::class);
        $i->update(['stock' => 1]);
    }

    public function test_cart_keeps_numbers_separate_and_deletes_only_selected_unit(): void
    {
        $i = $this->setupItem();
        foreach ([1, 2] as $unit) {
            $this->post('/keranjang', ['catalog_item_id' => $i->id, 'unit_number' => $unit])->assertSessionHasNoErrors();
        }
        $this->assertCount(2, app(ShoppingCart::class)->lines());
        $this->get('/keranjang')->assertSee('PlayStation 4 · 01')->assertSee('PlayStation 4 · 02')->assertSee('unit_number=2');
        $this->delete('/keranjang/'.$i->id, ['unit_number' => 1]);
        $lines = app(ShoppingCart::class)->lines();
        $this->assertCount(1, $lines);
        $this->assertSame(2, $lines->first()['unit_number']);
    }

    public function test_automatic_legacy_booking_gets_a_free_number_when_confirmed(): void
    {
        $i = $this->setupItem();
        $m = app(RentalReservationManager::class);
        $a = $m->create($this->data($i, 1));
        $m->adminAction($a, 'confirm');
        $data = $this->data($i, 1);
        unset($data['unit_number']);
        $b = $m->create($data);
        $m->adminAction($b,'confirm');
        $this->assertSame(2,$b->fresh()->unit_number);
    }
}
