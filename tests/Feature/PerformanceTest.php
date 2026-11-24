<?php

namespace Tests\Feature;

use App\Support\SiteAsset;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    public function test_root_renders_login_without_a_redirect_or_eager_third_party_widget(): void
    {
        $this->get('/')->assertOk()->assertSee('MASUK AKUN')->assertSee('verification-start')->assertDontSee('src="https://challenges.cloudflare.com', false)->assertSee('as="image"', false)->assertDontSee('fonts.googleapis.com');
    }

    public function test_optimized_assets_exist_and_match_current_source_files(): void
    {
        foreach (config('performance-assets.files') as $source => $file) {
            $this->assertFileExists(public_path($file));
        }
        foreach (config('performance-assets.source_hashes') as $source => $expected) {
            $this->assertSame($expected, hash('sha256', str_replace("\r\n", "\n", file_get_contents(public_path($source)))), $source.' needs asset rebuild');
        }
        $this->assertLessThan(30000, filesize(public_path(config('performance-assets.files')['logo-ps-seleraku-transparent.png'])));
        $this->assertLessThan(15000, filesize(public_path(config('performance-assets.login.mobile'))));
    }

    public function test_production_uses_fingerprinted_assets_but_external_images_still_work(): void
    {
        $this->app->instance('env', 'production');
        $this->assertStringContainsString('/optimized/login.', SiteAsset::url('login.js'));
        $this->get('/')->assertOk()->assertSee('<style>', false)->assertDontSee('rel="stylesheet"', false)->assertSee('srcset=', false);
        $this->assertStringContainsString('font-family:Poppins', SiteAsset::styles('fonts/poppins-v24.css'));
        $this->assertSame('https://example.com/custom.jpg', SiteAsset::url('https://example.com/custom.jpg'));
    }
}
