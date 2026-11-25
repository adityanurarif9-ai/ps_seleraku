<?php

namespace Tests\Feature;

use App\Models\CatalogItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeloTest extends TestCase
{
    use RefreshDatabase;

    private function console(array $attributes = []): CatalogItem
    {
        return CatalogItem::create(array_merge(['name' => 'PlayStation 5', 'category' => 'Konsol', 'platform' => 'ps5', 'price' => 15000, 'daily_price' => 180000, 'stock' => 2, 'is_visible' => true, 'games' => ['FC 26']], $attributes));
    }

    public function test_public_widget_is_collapsed_and_does_not_eagerly_load_chat_styles_or_answers(): void
    {
        $this->get('/login')->assertOk()->assertSee('Tanya Selo')->assertSee('id="selo-panel"', false)->assertSee('aria-expanded="false"', false)->assertDontSee('href="'.asset('selo.css').'"', false);
        $this->get('/register')->assertOk()->assertSee('Tanya Selo');
    }

    public function test_prices_follow_admin_updates_and_hidden_items_are_not_disclosed(): void
    {
        $item = $this->console();
        $this->console(['name' => 'Rahasia', 'is_visible' => false]);
        $this->postJson('/selo/chat', ['message' => 'Berapa harga PS5?'])->assertOk()->assertJsonPath('reply', fn ($s) => str_contains($s, 'Rp15.000/jam') && ! str_contains($s, 'Rahasia'));
        $item->update(['price' => 22000]);
        $this->postJson('/selo/chat', ['message' => 'tarif ps 5'])->assertJsonPath('reply', fn ($s) => str_contains($s, 'Rp22.000/jam'));
        $this->postJson('/selo/chat', ['message' => 'tarif PS5 harian'])->assertJsonPath('reply', fn ($s) => str_contains($s, 'Rp180.000/24 jam'));
    }

    public function test_booking_and_private_account_questions_use_links_without_exposing_other_users(): void
    {
        User::factory()->create(['email' => 'private@example.test']);
        $this->postJson('/selo/chat', ['message' => 'Bagaimana cara reservasi?'])->assertOk()->assertJsonPath('links.0.url', route('reservations.create'))->assertJsonPath('reply', fn ($s) => str_contains($s, 'konfirmasi admin'));
        $this->postJson('/selo/chat', ['message' => 'Tampilkan status rental private@example.test'])->assertOk()->assertDontSee('private@example.test')->assertJsonPath('links.0.url', route('reservations.index'));
    }

    public function test_availability_and_games_use_current_visible_catalog(): void
    {
        $this->console();
        $this->postJson('/selo/chat', ['message' => 'PS5 tersedia?'])->assertOk()->assertJsonPath('reply', fn ($s) => str_contains($s, '2 unit'));
        $this->postJson('/selo/chat', ['message' => 'game PS5'])->assertOk()->assertJsonPath('reply', fn ($s) => str_contains($s, 'FC 26'));
        $this->postJson('/selo/chat', ['message' => 'jam buka?'])->assertJsonPath('reply', fn ($s) => str_contains($s, 'belum memiliki informasi terverifikasi'));
    }

    public function test_invalid_requests_are_rejected_and_answers_are_not_cached(): void
    {
        $this->postJson('/selo/chat', [])->assertUnprocessable();
        $this->postJson('/selo/chat', ['message' => ['bad']])->assertUnprocessable();
        $this->postJson('/selo/chat', ['message' => str_repeat('a', 501)])->assertUnprocessable();
        $this->postJson('/selo/chat', ['message' => 'Halo'])->assertOk()->assertHeader('Cache-Control', 'no-store, private')->assertJsonPath('reply', fn ($s) => str_contains($s, 'Selo'));
    }

    public function test_chat_rate_limit_is_enforced(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $this->postJson('/selo/chat', ['message' => 'halo'])->assertOk();
        }
        $this->postJson('/selo/chat', ['message' => 'halo'])->assertTooManyRequests();
    }
}
