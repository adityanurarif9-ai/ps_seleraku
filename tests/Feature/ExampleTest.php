<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.turnstile.site_key' => null, 'services.turnstile.secret' => null]);
        $this->seed(DatabaseSeeder::class);
    }

    public function test_protected_routes_require_login(): void
    {
        $this->get('/')->assertOk()->assertSee('MASUK AKUN');
        $this->get('/home-page')->assertRedirect(route('login'));
        $this->get('/page-ke2')->assertRedirect(route('login'));
        $this->get('/sewa-harian')->assertRedirect(route('login'));
        $this->get('/menu-fnb')->assertRedirect(route('login'));
        $this->get('/home page')->assertRedirect(route('login'));
        $this->get('/page ke2')->assertRedirect(route('login'));
    }

    public function test_login_page_renders_correctly(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200)
            ->assertSee('Reservasi konsol.')
            ->assertSee('Akun pemain')
            ->assertSee('Akun admin');
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

        $this->get('/catalog')->assertNotFound();

        $this->get(route('home-page'))
            ->assertOk()
            ->assertSee('Beranda')
            ->assertSee('Konsol &amp; Game', false)
            ->assertDontSee('Panel Admin')
            ->assertSee(route('tentang'))
            ->assertSee('Pilihan sewa harian.')
            ->assertSee('SEWA PLAYSTATION 4')
            ->assertSee('SEWA PLAYSTATION 5')
            ->assertSee('SEWA PLAYSTATION 3')
            ->assertSee('SEWA NINTENDO SWITCH')
            ->assertSee('Jam operasional akan diinformasikan pengelola.')
            ->assertSee('Jelajahi')
            ->assertSee('Kunjungi kami')
            ->assertSee(route('kontak'));

        $this->get('/home page')->assertOk();

        $this->get(route('page-ke2'))
            ->assertOk()
            ->assertSee('PILIH PLAYSTATION')
            ->assertSee('PlayStation 3')
            ->assertSee('Nintendo Switch')
            ->assertSee('PlayStation 5')
            ->assertSee('PlayStation 4')
            ->assertSee('Jelajahi')
            ->assertSee('Kunjungi kami')
            ->assertSee(route('kontak'));

        $this->get('/page ke2')->assertOk();

        $this->get(route('sewa-harian'))
            ->assertOk()
            ->assertSee('PlayStation 5')
            ->assertSee('Rp 150.000')
            ->assertSee('Nintendo Switch')
            ->assertSee('Sewa Harian')
            ->assertSee('Jelajahi');

        $this->get(route('menu-fnb'))
            ->assertOk()
            ->assertSee('Menu Camilan & Minuman Barista')
            ->assertSee('Indomie Goreng Double + Telur Kornet')
            ->assertSee('Kentang Goreng Truffle Crinkle')
            ->assertSee('Kopi Susu Gula Aren Barista')
            ->assertSee('Peralatan Higienis & Controller Friendly')
            ->assertSee('Hubungi pengelola')->assertSee(route('kontak'))->assertSee('Tambah ke keranjang');

        $this->get('/admin/catalog-items')->assertRedirect(url('/admin/login'));

        $this->post(route('logout'))
            ->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_admin_can_login_and_access_admin_panel(): void
    {
        $this->post('/login', [
            'email' => 'admin@psseleraku.test',
            'password' => 'Seleraku!2026',
        ])->assertRedirect(url('/admin'));

        $this->assertAuthenticatedAs(User::where('email', 'admin@psseleraku.test')->first());

        $this->get('/admin')->assertOk()->assertSee('Dashboard PS Seleraku');
        $this->get('/admin/catalog-items')->assertOk();
        $this->get(route('home-page'))->assertOk()->assertSee('Panel Admin');

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
