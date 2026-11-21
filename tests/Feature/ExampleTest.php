<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_protected_routes_require_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get('/home-page')->assertRedirect(route('login'));
        $this->get('/page-ke2')->assertRedirect(route('login'));
        $this->get('/home page')->assertRedirect(route('login'));
        $this->get('/page ke2')->assertRedirect(route('login'));
    }

    public function test_login_page_renders_correctly(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200)
            ->assertSee('Masuk ke akunmu')
            ->assertSee('Pemain')
            ->assertSee('Admin');
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'pemain@psseleraku.test',
            'password' => 'SalahPassword123',
        ]);

        $response->assertRedirect('/login')
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_pemain_can_login_navigate_all_pages_and_logout(): void
    {
        $login = $this->post('/login', [
            'email' => 'Pemain@psseleraku.test',
            'password' => 'Mabar!2026',
        ]);

        $login->assertRedirect(route('home-page'));
        $this->assertAuthenticated();

        $this->get(route('catalog'))
            ->assertOk()
            ->assertSee('Katalog')
            ->assertSee('Home Page')
            ->assertSee('Unit PS (Page Ke2)')
            ->assertDontSee('Panel admin');

        $this->get(route('home-page'))
            ->assertOk()
            ->assertSee('KENALAN DENGAN')
            ->assertSee('PILIHAN PAKET RENTAL TERBAIK')
            ->assertSee('PAKET PS4 REGULER')
            ->assertSee('PAKET PS5 NEXT-GEN')
            ->assertSee('PAKET WEEKEND')
            ->assertSee('PAKET MINGGUAN PRO')
            ->assertSee('Operasional: Buka 24 Jam Non-Stop')
            ->assertSee('Follow kami di')
            ->assertSee('Temukan Lokasi Kami')
            ->assertSee('Lihat di Google Maps');

        $this->get('/home page')->assertOk();

        $this->get(route('page-ke2'))
            ->assertOk()
            ->assertSee('PILIH PLAYSTATION')
            ->assertSee('PS3 01')
            ->assertSee('Nintendo 01')
            ->assertSee('PS5 Disc Edition Ultra')
            ->assertSee('PS4 Pro 1TB 4K Enhanced')
            ->assertSee('Follow kami di')
            ->assertSee('Temukan Lokasi Kami')
            ->assertSee('Lihat di Google Maps');

        $this->get('/page ke2')->assertOk();

        $this->get('/admin/catalog-items')->assertForbidden();

        $this->post(route('logout'))
            ->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_admin_can_login_and_access_admin_panel(): void
    {
        $this->post('/login', [
            'email' => 'admin@psseleraku.test',
            'password' => 'Seleraku!2026',
        ])->assertRedirect(route('home-page'));

        $this->assertAuthenticatedAs(User::where('email', 'admin@psseleraku.test')->first());

        $this->get(route('catalog'))
            ->assertOk()
            ->assertSee('Panel admin');

        $this->get('/admin/catalog-items')->assertOk();

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
