<?php

namespace Tests\Feature;

use App\Filament\Resources\Pages\ManageFoodOrders;
use App\Filament\Resources\Pages\ManageRentalReservations;
use App\Models\CatalogItem;
use App\Models\FoodOrder;
use App\Models\User;
use App\Services\FoodOrderManager;
use App\Services\PlaySessionManager;
use App\Services\RentalReservationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class FoodOrderTest extends TestCase
{
    use RefreshDatabase;

    private function food(int $stock = 3): CatalogItem
    {
        return CatalogItem::create(['name' => 'Nasi Goreng '.Str::random(5), 'category' => 'Makanan', 'price' => 15000, 'stock' => $stock]);
    }

    public function test_checkout_uses_server_prices_notifies_admin_and_is_idempotent(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $player = User::factory()->create(['is_admin' => false]);
        $food = $this->food();
        $console = CatalogItem::create(['name' => 'PS4', 'category' => 'Konsol', 'price' => 10000, 'stock' => 2]);
        $this->actingAs($player)->withSession(['shopping_cart' => [$food->id => ['quantity' => 2, 'mode' => 'lounge'], $console->id => ['quantity' => 1, 'mode' => 'daily']]]);
        $key = (string) Str::uuid();
        $this->post('/pesanan-makanan', ['request_key' => $key, 'total_price' => 1, 'user_id' => $admin->id, 'status' => 'completed', 'notes' => 'Tanpa sambal'])->assertSessionHasNoErrors()->assertRedirect(route('food-orders.index'));
        $order = FoodOrder::firstOrFail();
        $this->assertSame($player->id, $order->user_id);
        $this->assertSame(30000, $order->total_price);
        $this->assertSame('pending', $order->status);
        $this->assertSame(1, $food->fresh()->stock);
        $this->assertSame(1, session('shopping_cart.'.$console->id.'.quantity'));
        $this->assertSame(1, $admin->notifications()->count());
        $this->assertSame('Pesanan kedai baru', $admin->notifications()->first()->data['title']);
        $this->assertSame(0, $player->notifications()->count());
        $this->post('/pesanan-makanan', ['request_key' => $key])->assertSessionHasNoErrors();
        $this->assertSame(1, FoodOrder::count());
        $this->assertSame(1, $admin->notifications()->count());
        $this->get('/pesanan-makanan')->assertOk()->assertSee('Tanpa sambal')->assertSee('Rp30.000');
    }

    public function test_competing_order_cannot_oversell_and_hidden_food_rolls_back(): void
    {
        $food = $this->food(1);
        $player = User::factory()->create();
        $cart = [$food->id => ['quantity' => 1, 'mode' => 'lounge']];
        $this->actingAs($player)->withSession(['shopping_cart' => $cart])->post('/pesanan-makanan', ['request_key' => (string) Str::uuid()])->assertSessionHasNoErrors();
        $this->actingAs(User::factory()->create())->withSession(['shopping_cart' => $cart])->post('/pesanan-makanan', ['request_key' => (string) Str::uuid()])->assertSessionHasErrors('order');
        $this->assertSame(1, FoodOrder::count());
        $this->assertSame(0, $food->fresh()->stock);
        $hidden = $this->food(3);
        $hidden->update(['is_visible' => false]);
        $valid = $this->food(3);
        $this->withSession(['shopping_cart' => [$valid->id => ['quantity' => 1], $hidden->id => ['quantity' => 1]]])->post('/pesanan-makanan', ['request_key' => (string) Str::uuid()])->assertSessionHasErrors('order');
        $this->assertSame(3, $valid->fresh()->stock);
        $this->assertSame(1, FoodOrder::count());
    }

    public function test_owner_only_history_and_cancel_releases_stock_once(): void
    {
        $food = $this->food();
        $owner = User::factory()->create();
        $this->actingAs($owner)->withSession(['shopping_cart' => [$food->id => ['quantity' => 1]]])->post('/pesanan-makanan', ['request_key' => (string) Str::uuid()]);
        $order = FoodOrder::firstOrFail();
        $this->actingAs(User::factory()->create());
        $this->get('/pesanan-makanan')->assertDontSee($order->reference());
        $this->post(route('food-orders.cancel', $order))->assertForbidden();
        $this->get('/admin/food-orders')->assertRedirect(url('/admin/login'));
        $this->actingAs($owner)->post(route('food-orders.cancel', $order))->assertSessionHasNoErrors();
        $this->post(route('food-orders.cancel', $order))->assertSessionHasNoErrors();
        $this->assertSame(3, $food->fresh()->stock);
        $this->assertSame('cancelled', $order->fresh()->status);
        auth()->logout();
        $this->get('/pesanan-makanan')->assertRedirect(route('login'));
    }

    public function test_admin_can_prepare_finish_or_reject_and_catalog_history_cannot_be_deleted(): void
    {
        $food = $this->food();
        $player = User::factory()->create();
        $this->actingAs($player)->withSession(['shopping_cart' => [$food->id => ['quantity' => 1]]])->post('/pesanan-makanan', ['request_key' => (string) Str::uuid()]);
        $order = FoodOrder::firstOrFail();
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        $this->assertFalse($admin->can('delete', $food));
        Livewire::test(ManageFoodOrders::class)->assertCanSeeTableRecords([$order])->callTableAction('preparing', $order)->assertHasNoTableActionErrors();
        $this->assertSame('preparing', $order->fresh()->status);
        $this->actingAs($player)->post(route('food-orders.cancel', $order))->assertSessionHasErrors('order');
        $this->actingAs($admin);
        Livewire::test(ManageFoodOrders::class)->callTableAction('ready', $order)->assertHasNoTableActionErrors();
        Livewire::test(ManageFoodOrders::class)->callTableAction('completed', $order)->assertHasNoTableActionErrors();
        $this->assertSame('completed', $order->fresh()->status);
        $this->assertSame(2, $food->fresh()->stock);
        $this->actingAs($player)->withSession(['shopping_cart' => [$food->id => ['quantity' => 1]]])->post('/pesanan-makanan', ['request_key' => (string) Str::uuid()]);
        $second = FoodOrder::latest('id')->first();
        $this->actingAs($admin);
        app(FoodOrderManager::class)->change($second, 'rejected');
        app(FoodOrderManager::class)->change($second, 'rejected');
        $this->assertSame(2, $food->fresh()->stock);
    }

    public function test_future_reservation_can_explicitly_start_now_without_losing_duration(): void
    {
        $this->freezeTime();
        $player = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $console = CatalogItem::create(['name' => 'PS4', 'category' => 'Konsol', 'price' => 10000, 'stock' => 1]);
        $this->actingAs($player);
        $r = app(RentalReservationManager::class)->create(['catalog_item_id' => $console->id, 'mode' => 'lounge', 'duration' => 60, 'starts_at' => now()->addHour()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i')]);
        $this->assertSame(1, $admin->notifications()->count());
        $this->actingAs($admin);
        app(RentalReservationManager::class)->adminAction($r, 'confirm');
        Livewire::test(ManageRentalReservations::class)->callTableAction('start', $r)->assertNotified('Reservasi belum dapat diproses');
        $this->assertSame('confirmed', $r->fresh()->status);
        Livewire::test(ManageRentalReservations::class)->callTableAction('startNow', $r)->assertHasNoTableActionErrors()->assertNotified('Sesi berhasil dimulai');
        $r->refresh();
        $this->assertSame('playing', $r->status);
        $this->assertTrue($r->starts_at->timestamp === now()->timestamp);
        $this->assertSame(3600, $r->playSession->remaining());
        $this->assertTrue($r->ends_at->timestamp === now()->addHour()->timestamp);
        $this->actingAs($player);
        app(RentalReservationManager::class)->requestExtension($r, 15);
        $this->assertSame(2, $admin->notifications()->count());
    }

    public function test_start_now_still_blocks_conflicting_unit_and_preserves_original_schedule(): void
    {
        $this->freezeTime();
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        $console = CatalogItem::create(['name' => 'PS3', 'category' => 'Konsol', 'price' => 6000, 'stock' => 1]);
        $m = app(RentalReservationManager::class);
        $r = $m->create(['catalog_item_id' => $console->id, 'mode' => 'lounge', 'duration' => 60, 'starts_at' => now()->addHours(2)->timezone('Asia/Jakarta')->format('Y-m-d\TH:i')]);
        $r = $m->adminAction($r, 'confirm');
        $original = $r->starts_at->toISOString();
        app(PlaySessionManager::class)->start(['catalog_item_id' => $console->id, 'customer_name' => 'Walk-in', 'minutes' => 60]);
        Livewire::test(ManageRentalReservations::class)->callTableAction('startNow', $r)->assertNotified('Reservasi belum dapat diproses');
        $this->assertSame('confirmed',$r->fresh()->status);
        $this->assertSame($original,$r->fresh()->starts_at->toISOString());
    }
}
