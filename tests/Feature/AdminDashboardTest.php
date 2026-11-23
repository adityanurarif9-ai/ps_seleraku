<?php

namespace Tests\Feature;

use App\Filament\Auth\AdminLogin;
use App\Filament\Widgets\CatalogOverview;
use App\Filament\Widgets\LowStockItems;
use App\Models\CatalogItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_must_login_before_viewing_admin(): void
    {
        $this->get('/admin')->assertRedirect(url('/admin/login'));
        $this->get('/admin/login')->assertOk()->assertSee('Masuk sebagai admin');
        $this->get('/login/admin')->assertRedirect('/admin/login');
    }

    public function test_player_cannot_access_dashboard_or_catalog_management(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]));
        $this->get('/admin')->assertRedirect(url('/admin/login'));
        $this->get('/admin/login')->assertOk()->assertSee('Masuk sebagai admin');
        $this->get('/admin/catalog-items')->assertRedirect(url('/admin/login'));
        Livewire::test(CatalogOverview::class)->assertForbidden();
        Livewire::test(LowStockItems::class)->assertForbidden();
    }

    public function test_admin_can_view_dashboard_with_real_stock_and_manage_catalog(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        CatalogItem::create(['name' => 'PS5 Test', 'category' => 'Konsol', 'price' => 15000, 'stock' => 1]);
        CatalogItem::create(['name' => 'Teh Test', 'category' => 'Minuman', 'price' => 5000, 'stock' => 10]);
        $this->get('/admin')->assertOk()->assertSee('Dashboard PS Seleraku')->assertSee('Unit konsol tersedia')->assertSee('PS5 Test')->assertDontSee('Teh Test');
        $this->get('/admin/catalog-items')->assertOk()->assertSee('Teh Test');
    }

    public function test_admin_login_opens_dashboard_and_player_login_opens_home(): void
    {
        config(['services.turnstile.site_key' => null, 'services.turnstile.secret' => null]);
        $admin = User::factory()->create(['is_admin' => true, 'password' => 'AdminTest!2026']);
        $this->post('/login', ['email' => $admin->email, 'password' => 'AdminTest!2026'])->assertRedirect(url('/admin'));
        $this->post('/logout')->assertRedirect(route('login'));
        $player = User::factory()->create(['is_admin' => false, 'password' => 'PlayerTest!2026']);
        $this->post('/login', ['email' => $player->email, 'password' => 'PlayerTest!2026'])->assertRedirect(route('home-page'));
    }

    public function test_separate_admin_login_accepts_admin_and_rejects_player(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'password' => 'AdminTest!2026']);
        Livewire::test(AdminLogin::class)
            ->fillForm(['email' => $admin->email, 'password' => 'AdminTest!2026', 'remember' => false])
            ->call('authenticate')->assertHasNoFormErrors()->assertRedirect(url('/admin'));
        $this->assertAuthenticatedAs($admin);
        auth()->logout();
        $player = User::factory()->create(['is_admin' => false, 'password' => 'PlayerTest!2026']);
        Livewire::test(AdminLogin::class)
            ->fillForm(['email' => $player->email, 'password' => 'PlayerTest!2026', 'remember' => false])
            ->call('authenticate')->assertHasFormErrors(['email']);
        $this->assertGuest();
    }
}
