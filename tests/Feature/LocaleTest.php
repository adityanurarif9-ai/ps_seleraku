<?php

namespace Tests\Feature;

use App\Models\CatalogItem;
use App\Models\RentalReservation;
use App\Models\SiteContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_language_switch_persists_and_rejects_unknown_languages_and_external_redirects(): void
    {
        $this->get('/login')->assertOk()->assertSee('MASUK AKUN')->assertSee('aria-label="English"', false);
        $this->from('/login')->post('/language', ['locale'=>'en'])->assertStatus(303)->assertRedirect('/login')->assertSessionHas('locale', 'en');
        $this->get('/login')->assertOk()->assertSee('<html lang="en">', false)->assertSee('SIGN IN NOW')->assertSee('Sign in with Google');
        $this->get('/register')->assertOk()->assertSee('CREATE ACCOUNT')->assertSee('FULL NAME');
        $this->get('/cara-sewa')->assertOk()->assertSee('HOW TO RENT')->assertSee('Choose a console and rental type');
        $this->post('/language', ['locale'=>'unknown'])->assertSessionHasErrors('locale');
        $this->from('https://untrusted.example/path')->post('/language', ['locale'=>'id'])->assertRedirect(route('login'));
        $this->get('/login')->assertSee('MASUK AKUN')->assertSee('<html lang="id">', false);
    }

    public function test_english_booking_uses_original_catalog_keys_and_server_prices(): void
    {
        $player=User::factory()->create();
        $item=CatalogItem::create(['name'=>'PlayStation 5', 'category'=>'Konsol', 'price'=>15000, 'daily_price'=>150000, 'stock'=>2]);
        $this->actingAs($player)->withSession(['locale'=>'en']);
        $this->get('/reservasi/baru')->assertOk()->assertSee('PlayStation 5')->assertSee('Choose a console')->assertSee('Submit booking');
        $payload=['catalog_item_id'=>$item->id, 'mode'=>'lounge', 'duration'=>60, 'starts_at'=>now()->addHours(2)->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i'), 'notes'=>'Catatan asli pelanggan'];
        $this->getJson('/reservasi/ketersediaan?'.http_build_query($payload))->assertOk()->assertJsonPath('total_price',15000)->assertJsonPath('available',2);
        $this->post('/reservasi',$payload)->assertSessionHasNoErrors()->assertRedirect();
        $r=RentalReservation::firstOrFail();
        $this->get('/reservasi/'.$r->id)->assertOk()->assertSee('Awaiting confirmation')->assertSee('Catatan asli pelanggan')->assertSee('Cancel booking');
        $this->assertSame('Konsol',$item->fresh()->category);
        $this->assertSame(15000,$r->total_price);
    }

    public function test_english_content_and_selo_follow_locale_without_overwriting_original_content(): void
    {
        SiteContent::create(['id'=>1,'data'=>['hero_title'=>'Judul asli', 'hero_title_en'=>'Our gaming lounge']]);
        $this->actingAs(User::factory()->create())->withSession(['locale'=>'en']);
        $this->get('/home-page')->assertOk()->assertSee('Our gaming lounge')->assertSee('MY RENTALS');
        $this->postJson('/selo/chat',['message'=>'How do I book?'])->assertOk()->assertJsonPath('links.0.label','Book a session')->assertJsonPath('reply',fn ($reply)=>str_contains($reply,'Track confirmation in My Rentals'));
        $this->assertSame('Judul asli',SiteContent::values()['hero_title']);
        $this->withSession(['locale'=>'id'])->get('/home-page')->assertSee('Judul asli');
    }
}
