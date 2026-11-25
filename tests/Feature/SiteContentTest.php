<?php

namespace Tests\Feature;

use App\Filament\Pages\WebsiteSettings;
use App\Models\SiteContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiteContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_information_pages_are_separate_and_available_without_login(): void
    {
        foreach (['tentang' => 'Tentang Kami', 'cara-sewa' => 'Cara Sewa', 'kontak' => 'Kontak'] as $path => $title) {
            $this->get('/'.$path)->assertOk()->assertSee('<title>'.$title.' · PS Seleraku</title>', false)->assertSee('name="viewport"', false)->assertSee('<body ', false)->assertSee(route('tentang'))->assertSee(route('cara-sewa'))->assertSee(route('kontak'));
        }
        $this->get('/reservasi/baru')->assertRedirect(route('login'));
    }

    public function test_admin_changes_update_website_footer_and_selo(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $data = array_replace(SiteContent::defaults(), ['hero_title' => 'Judul dari pengelola', 'about_title' => 'Cerita baru pengelola', 'about_body' => 'Deskripsi usaha yang baru.', 'address' => 'Alamat Uji No. 12', 'whatsapp' => '6281234567890', 'contact_email' => 'info@example.test', 'opening_hours' => 'Setiap hari 10.00–22.00']);
        $this->get('/admin/isi-website')->assertOk()->assertSee('Isi Website');
        Livewire::test(WebsiteSettings::class)->fillForm($data)->call('save')->assertHasNoFormErrors();
        $this->assertSame('Judul dari pengelola', SiteContent::values()['hero_title']);
        $this->get('/home-page')->assertOk()->assertSee('Judul dari pengelola')->assertSee('Alamat Uji No. 12')->assertSee('6281234567890');
        $this->get('/tentang')->assertSee('Cerita baru pengelola')->assertSee('Deskripsi usaha yang baru.');
        $this->get('/kontak')->assertSee('info@example.test')->assertSee('Setiap hari 10.00–22.00');
        $this->postJson('/selo/chat', ['message' => 'jam buka dan alamat?'])->assertOk()->assertJsonPath('reply', fn ($reply) => str_contains($reply, 'Alamat Uji No. 12') && str_contains($reply, '10.00–22.00'));
        $this->assertDatabaseCount('site_contents', 1);
    }

    public function test_player_cannot_save_settings_and_validation_rejects_unsafe_contact_links(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get('/admin/isi-website')->assertRedirect();
        Livewire::test(WebsiteSettings::class)->assertForbidden();
        $this->assertDatabaseCount('site_contents', 0);
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        Livewire::test(WebsiteSettings::class)->fillForm(array_replace(SiteContent::defaults(), ['whatsapp' => 'javascript:bad', 'contact_email' => 'invalid']))->call('save')->assertHasFormErrors(['whatsapp', 'contact_email']);
        $this->assertDatabaseCount('site_contents', 0);
    }

    public function test_content_is_escaped_and_empty_contacts_do_not_use_examples(): void
    {
        SiteContent::create(['id' => 1, 'data' => array_replace(SiteContent::defaults(), ['about_title' => '<script>alert(1)</script>'])]);
        $this->get('/tentang')->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/kontak')->assertSee('Kontak langsung belum ditambahkan')->assertDontSee('Gaming Arc')->assertDontSee('concierge@psseleraku.id');
    }
}
