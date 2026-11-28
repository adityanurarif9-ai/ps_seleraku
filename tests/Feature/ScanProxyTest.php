<?php

namespace Tests\Feature;

use App\Models\CatalogItem;
use App\Models\RentalReservation;
use App\Models\TestPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ScanProxyTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        TrustProxies::flushState();
        Request::setTrustedProxies([], 0);
        parent::tearDown();
    }

    private function payment(): TestPayment
    {
        $u = User::factory()->create();
        $i = CatalogItem::firstOrCreate(['name' => 'PS4 proxy test', 'category' => 'Konsol'], ['price' => 10000, 'stock' => 1]);
        $r = RentalReservation::create(['user_id' => $u->id, 'catalog_item_id' => $i->id, 'mode' => 'lounge', 'duration_minutes' => 90, 'unit_rate' => 10000, 'total_price' => 15000, 'starts_at' => now()->addHour(), 'ends_at' => now()->addMinutes(150), 'status' => 'pending']);

        return TestPayment::create(['user_id' => $u->id, 'rental_reservation_id' => $r->id, 'amount' => 15000, 'status' => 'pending']);
    }

    public function test_fresh_absolute_signature_works_when_https_is_terminated_before_php(): void
    {
        $p = $this->payment();
        $this->app->instance('env', 'production');
        URL::forceRootUrl('https://ps.flexboxstudio.site');
        URL::forceScheme('https');
        $signed = $p->demoScanUrl();
        $upstream = preg_replace('/^https:/', 'http:', $signed);
        $this->withServerVariables(['REMOTE_ADDR' => '10.1.2.3'])->get($upstream, ['X-Forwarded-Proto' => 'https'])->assertOk()->assertSee('Bayar demo sekarang');
        $this->assertSame('pending', $p->fresh()->status);
        $this->get($upstream, ['X-Forwarded-Proto' => 'https', 'X-Forwarded-Host' => 'attacker.example', 'X-Forwarded-Port' => '444', 'X-Forwarded-For' => '1.2.3.4'])->assertOk();
        $request = $this->app['request'];
        $this->assertSame('ps.flexboxstudio.site', $request->getHost());
        $this->assertSame(443, $request->getPort());
        $this->assertSame('10.1.2.3', $request->ip());
        $other = $this->payment();
        $this->get(str_replace('/demo-bayar/'.$p->id, '/demo-bayar/'.$other->id, $upstream), ['X-Forwarded-Proto' => 'https'])->assertForbidden();
        $this->get($upstream.'&amount=1', ['X-Forwarded-Proto' => 'https'])->assertForbidden();
        $this->travel(31)->minutes();
        $this->get($upstream, ['X-Forwarded-Proto' => 'https'])->assertForbidden();
        $this->assertSame('pending', $p->fresh()->status);
    }

    public function test_public_client_cannot_spoof_https_by_supplying_a_forwarded_header(): void
    {
        $p = $this->payment();
        $this->app->instance('env', 'production');
        URL::forceRootUrl('https://ps.flexboxstudio.site');
        URL::forceScheme('https');
        $upstream = preg_replace('/^https:/', 'http:', $p->demoScanUrl());
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.42'])->get($upstream, ['X-Forwarded-Proto' => 'https'])->assertForbidden();
        $this->assertSame('http', $this->app['request']->getScheme());
        $this->assertSame('pending', $p->fresh()->status);
    }

    public function test_local_http_signature_still_works_and_invalid_qr_has_recovery_instructions(): void
    {
        $p = $this->payment();
        $this->get($p->demoScanUrl())->assertOk()->assertSee('Bayar demo sekarang');
        $this->get('/demo-bayar/'.$p->id)->assertForbidden()->assertSee('QR demo tidak berlaku')->assertSee('Muat ulang halaman pembayaran')->assertDontSee('Bayar demo sekarang');
    }
}
