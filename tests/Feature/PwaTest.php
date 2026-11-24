<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_registration_offer_installation_and_iphone_metadata(): void
    {
        foreach (['/login', '/register'] as $url) {
            $this->get($url)->assertOk()->assertSee('manifest.webmanifest')->assertSee('apple-mobile-web-app-capable')->assertSee('data-install-app', false)->assertSee('viewport-fit=cover')->assertSee('pwa.js')->assertSee('loading.js')->assertSee('app-splash');
        }
    }

    public function test_authenticated_pages_keep_manifest_and_registration_worker_script(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['/home-page', '/page-ke2', '/sewa-harian', '/menu-fnb', '/rental-saya', '/reservasi/baru'] as $url) {
            $this->get($url)->assertOk()->assertSee('manifest.webmanifest')->assertSee('pwa.js');
        }
    }

    public function test_manifest_icons_have_correct_dimensions_and_start_url_is_in_scope(): void
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('standalone', $manifest['display']);
        $this->assertSame('/', $manifest['scope']);
        $this->assertStringStartsWith('/home-page', $manifest['start_url']);
        foreach ($manifest['icons'] as $icon) {
            $info = getimagesize(public_path(ltrim($icon['src'], '/')));
            $this->assertSame($icon['sizes'], $info[0].'x'.$info[1]);
        }
        $info = getimagesize(public_path('icons/brand-v2-apple.png'));
        $this->assertSame(180, $info[0]);
    }
}
