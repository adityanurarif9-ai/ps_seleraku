<?php

namespace Tests\Feature;

use App\Filament\Resources\Pages\ManageCatalogItems;
use App\Models\CatalogItem;
use App\Models\PlaySession;
use App\Models\User;
use App\Services\RentalAvailability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WebsiteCatalogTest extends TestCase
{
    use RefreshDatabase;

    private function console(): CatalogItem
    {
        return CatalogItem::create(['name' => 'Console Sync Test', 'category' => 'Konsol', 'platform' => 'ps5', 'price' => 17000, 'daily_price' => 175000, 'stock' => 2, 'description' => 'Spesifikasi dari admin', 'image_path' => 'images/ps5-ultra.png', 'games' => ['Game dari admin'], 'is_visible' => true]);
    }

    public function test_admin_edit_changes_all_public_prices_names_games_and_reservation_selection(): void
    {
        $item = $this->console();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        Livewire::test(ManageCatalogItems::class)->callTableAction('edit', $item, data: ['name' => 'Konsol Baru Sinkron', 'category' => 'Konsol', 'platform' => 'ps5', 'price' => 19000, 'daily_price' => 195000, 'stock' => 3, 'description' => 'Deskripsi baru admin', 'image_path' => 'images/ps5-ultra.png', 'games' => ['Game Baru Admin'], 'is_visible' => true])->assertHasNoTableActionErrors();
        $this->assertSame(19000, $item->fresh()->price);
        $this->actingAs(User::factory()->create());
        $this->get('/page-ke2')->assertOk()->assertSee('Konsol Baru Sinkron')->assertSee('Rp 19.000')->assertSee('Game Baru Admin')->assertSee('Deskripsi baru admin')->assertDontSee('Console Sync Test');
        $this->get('/sewa-harian')->assertOk()->assertSee('Rp 195.000')->assertSee('Konsol Baru Sinkron');
        $this->get('/home-page')->assertOk()->assertSee('Rp 195.000')->assertSee('SEWA KONSOL BARU SINKRON');
        $this->get('/reservasi/baru?catalog_item_id='.$item->id)->assertOk()->assertSee('Konsol Baru Sinkron — 3 unit');
    }

    public function test_hidden_item_disappears_and_cannot_be_reserved_by_direct_post(): void
    {
        $item = $this->console();
        $item->update(['is_visible' => false]);
        $this->actingAs(User::factory()->create());
        foreach (['/page-ke2', '/sewa-harian', '/home-page', '/reservasi/baru'] as $url) {
            $this->get($url)->assertOk()->assertDontSee('Console Sync Test');
        }
        $this->post('/reservasi', ['catalog_item_id' => $item->id, 'mode' => 'lounge', 'duration' => 60, 'starts_at' => now()->setTimezone('Asia/Jakarta')->addHour()->format('Y-m-d\TH:i')])->assertSessionHasErrors('catalog_item_id');
        $this->assertDatabaseCount('rental_reservations', 0);
    }

    public function test_food_prices_stocks_and_visibility_follow_admin_data(): void
    {
        $item = CatalogItem::create(['name' => 'Minuman Admin Sync', 'category' => 'Minuman', 'price' => 13500, 'stock' => 7, 'description' => 'Resep dari admin']);
        $this->actingAs(User::factory()->create());
        $this->get('/menu-fnb')->assertOk()->assertSee('Minuman Admin Sync')->assertSee('Rp 13.500')->assertSee('Stok: 7');
        $item->update(['stock' => 0, 'price' => 14500]);
        $this->get('/menu-fnb')->assertOk()->assertSee('Rp 14.500')->assertSee('Stok habis');
        $item->update(['is_visible' => false]);
        $this->get('/menu-fnb')->assertDontSee('Minuman Admin Sync');
    }

    public function test_public_availability_uses_sessions_instead_of_example_statuses(): void
    {
        $item = $this->console();
        $admin = User::factory()->create(['is_admin' => true]);
        PlaySession::create(['catalog_item_id' => $item->id, 'created_by' => $admin->id, 'customer_name' => 'Aktif', 'status' => 'running', 'total_seconds' => 3600, 'remaining_seconds' => 3600, 'hourly_rate' => 17000, 'ends_at' => now()->addHour()]);
        $this->actingAs(User::factory()->create());
        $this->get('/page-ke2')->assertOk()->assertSee('Unit 01')->assertSee('Unit 02')->assertSee('periksa jadwal lain')->assertDontSee('Estimasi selesai pk 16:30');
        $this->assertSame(1, app(RentalAvailability::class)->available($item, now(), now()->addHour()));
    }
}
