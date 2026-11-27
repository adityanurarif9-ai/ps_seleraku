<?php

namespace Tests\Feature;

use App\Filament\Resources\Pages\ManageRentalReservations;
use App\Models\CatalogItem;
use App\Models\RentalReservation;
use App\Models\User;
use App\Services\PlaySessionManager;
use App\Services\RentalReservationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class RentalIdentityTest extends TestCase
{
    use RefreshDatabase;

    private function data(): array
    {
        $this->travelTo(now()->startOfMinute());
        $this->actingAs(User::factory()->create());
        $item = CatalogItem::create(['name' => 'PS4 identity test', 'category' => 'Konsol', 'stock' => 2, 'price' => 10000, 'daily_price' => 90000]);

        return ['catalog_item_id' => $item->id, 'mode' => 'daily', 'duration' => 1, 'starts_at' => now()->timezone('Asia/Jakarta')->addHour()->format('Y-m-d\TH:i'), 'document_type' => 'student', 'identity_consent' => '1', 'bring_guarantee' => '1', 'identity_photo' => UploadedFile::fake()->image('sample.jpg', 400, 240)];
    }

    public function test_daily_booking_requires_photo_and_both_acknowledgements(): void
    {
        $data = $this->data();
        unset($data['identity_photo'],$data['bring_guarantee'],$data['identity_consent']);
        $this->post('/reservasi', $data)->assertSessionHasErrors(['identity_photo', 'identity_consent', 'bring_guarantee']);
        $this->assertDatabaseCount('rental_reservations', 0);
        $this->assertDatabaseCount('rental_identities', 0);
        $this->get(route('reservations.availability', $data))->assertOk();
    }

    public function test_photo_is_encrypted_private_and_not_in_receipts_or_model_json(): void
    {
        $data = $this->data();
        $owner = auth()->user();
        $this->post('/reservasi', $data)->assertSessionHasNoErrors()->assertStatus(303);
        $r = RentalReservation::firstOrFail();
        $photo = $r->identity;
        $this->assertTrue($r->requires_identity_check);
        $this->assertStringNotContainsString('encrypted_photo', $photo->toJson());
        $plain = base64_decode(Crypt::decryptString($photo->encrypted_photo));
        $this->assertStringStartsWith("\xff\xd8", $plain);
        $this->assertNotSame($plain, $photo->encrypted_photo);
        $this->get(route('reservations.identity', $r))->assertOk()->assertHeader('Content-Type', 'image/jpeg')->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->get(route('receipts.rental', $r))->assertOk()->assertDontSee($photo->encrypted_photo, false);
        $this->actingAs(User::factory()->create())->get(route('reservations.identity', $r))->assertForbidden();
        $this->actingAs(User::factory()->create(['is_admin' => true]))->get(route('reservations.identity', $r))->assertOk();
        auth()->logout();
        $this->get(route('reservations.identity', $r))->assertRedirect(route('login'));
        $this->actingAs($owner);
        $r->update(['status' => 'cancelled']);
        $this->assertDatabaseCount('rental_identities', 0);
    }

    public function test_nonimages_and_oversized_files_are_rejected_and_lounge_needs_no_identity(): void
    {
        $data = $this->data();
        $data['identity_photo'] = UploadedFile::fake()->create('bad.svg', 1, 'image/svg+xml');
        $this->post('/reservasi', $data)->assertSessionHasErrors('identity_photo');
        $data['identity_photo'] = UploadedFile::fake()->image('huge.jpg', 400, 240)->size(2049);
        $this->post('/reservasi', $data)->assertSessionHasErrors('identity_photo');
        $data['mode'] = 'lounge';
        $data['duration'] = 60;
        unset($data['identity_photo'],$data['identity_consent'],$data['bring_guarantee'],$data['document_type']);
        $this->post('/reservasi', $data)->assertSessionHasNoErrors()->assertStatus(303);
        $this->assertDatabaseCount('rental_identities', 0);
    }

    public function test_expired_photo_is_inaccessible_and_removed(): void
    {
        $data = $this->data();
        $this->post('/reservasi', $data)->assertSessionHasNoErrors();
        $r = RentalReservation::firstOrFail();
        $this->travelTo($r->identity->expires_at->addSecond());
        $this->get(route('reservations.identity', $r))->assertNotFound();
        $this->assertDatabaseCount('rental_identities', 0);
    }

    public function test_daily_unit_requires_admin_collection_check_before_start_and_photo_is_deleted_on_finish(): void
    {
        config(['payments.demo_enabled' => false]);
        $data = $this->data();
        $this->post('/reservasi', $data)->assertSessionHasNoErrors();
        $r = RentalReservation::firstOrFail();
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        $m = app(RentalReservationManager::class);
        $m->adminAction($r, 'confirm');
        try {
            $m->adminAction($r->fresh(), 'start_now');
            $this->fail('Collection check required');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('identity', $e->errors());
        }
        $page = Livewire::test(ManageRentalReservations::class);
        $page->callTableAction('pickupCheck', $r, ['original_checked' => false, 'guarantee_received' => false])->assertHasTableActionErrors();
        $this->assertNull($r->fresh()->pickup_verified_at);
        $page = Livewire::test(ManageRentalReservations::class);
        $page->callTableAction('pickupCheck', $r, ['original_checked' => true, 'guarantee_received' => true])->assertHasNoTableActionErrors();
        $this->assertNotNull($r->fresh()->pickup_verified_at);
        $m->adminAction($r->fresh(), 'start_now');
        app(PlaySessionManager::class)->change($r->fresh()->playSession, 'finish');
        $this->assertDatabaseCount('rental_identities', 0);
    }

    public function test_cart_suggests_live_food_drinks_and_snacks_with_quantity_controls(): void
    {
        $this->actingAs(User::factory()->create());
        $food = CatalogItem::create(['name' => 'Mie live', 'category' => 'Makanan', 'price' => 10000, 'stock' => 3]);
        CatalogItem::create(['name' => 'Teh live', 'category' => 'Minuman', 'price' => 4000, 'stock' => 2]);
        CatalogItem::create(['name' => 'Hidden food', 'category' => 'Makanan', 'price' => 1, 'stock' => 3, 'is_visible' => false]);
        $this->get('/keranjang')->assertOk()->assertSee('Mau sekalian pesan menu?')->assertSee('Mie live')->assertSee('Teh live')->assertDontSee('Hidden food');
        $this->post('/keranjang', ['catalog_item_id' => $food->id]);
        $this->get('/keranjang')->assertSee('Kurangi Mie live')->assertSee('Rp10.000');
        $this->post('/keranjang', ['catalog_item_id' => $food->id]);
        $this->assertSame(2, session('shopping_cart.'.$food->id.'.quantity'));
        $food->update(['price' => 12000]);
        $this->get('/keranjang')->assertSee('Rp24.000');
    }
}
