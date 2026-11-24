<?php

namespace Tests\Feature;

use App\Models\User;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as HttpRequest;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;
use Mockery;
use Tests\TestCase;

class RegistrationAndGoogleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.turnstile.site_key' => null, 'services.turnstile.secret' => null, 'services.google.client_id' => 'test-client', 'services.google.client_secret' => 'test-secret', 'services.google.redirect' => 'http://localhost/auth/google/callback']);
    }

    private function data(array $extra = []): array
    {
        return array_merge(['name' => '  Pemain Baru  ', 'email' => '  BARU@example.test  ', 'password' => 'AmanMain2026!', 'password_confirmation' => 'AmanMain2026!'], $extra);
    }

    private function identity(string $id = 'google-123', string $email = 'baru@example.test', bool $verified = true): GoogleUser
    {
        return (new GoogleUser)->setRaw(['email_verified' => $verified])->map(['id' => $id, 'email' => $email, 'name' => 'Pemain Google']);
    }

    private function provider($identity = null, $exception = null): void
    {
        $p = Mockery::mock();
        $p->shouldReceive('setHttpClient')->once()->andReturnSelf();
        if ($exception) {
            $p->shouldReceive('user')->once()->andThrow($exception);
        } else {
            $p->shouldReceive('user')->once()->andReturn($identity ?? $this->identity());
        }
        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($p);
    }

    private function googleCallback(array $session = [])
    {
        return $this->withSession(array_merge(['google_oauth_states' => ['good-state' => time()]], $session))->get('/auth/google/callback?state=good-state&code=example-code');
    }

    public function test_registration_form_and_login_link_are_real_pages(): void
    {
        $this->get('/login')->assertOk()->assertSee(route('register'))->assertDontSee('register-help');
        $this->get('/register')->assertOk()->assertSee('name="password_confirmation"', false)->assertSee('name="name"', false);
    }

    public function test_registration_normalizes_email_hashes_password_and_never_grants_admin(): void
    {
        $this->post('/register', $this->data(['is_admin' => true, 'email_verified_at' => now()]))->assertRedirect(route('home-page'));
        $user = User::firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('baru@example.test', $user->email);
        $this->assertSame('Pemain Baru', $user->name);
        $this->assertFalse($user->is_admin);
        $this->assertNull($user->email_verified_at);
        $this->assertTrue(Hash::check('AmanMain2026!', $user->password));
        $this->post('/logout');
        $this->post('/login', ['email' => 'BARU@example.test', 'password' => 'AmanMain2026!'])->assertRedirect(route('home-page'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_rejects_duplicate_weak_and_mismatched_passwords(): void
    {
        User::factory()->create(['email' => 'baru@example.test']);
        $this->from('/register')->post('/register', $this->data())->assertSessionHasErrors('email');
        $this->post('/register', $this->data(['email' => 'other@example.test', 'password' => 'short', 'password_confirmation' => 'other']))->assertSessionHasErrors('password');
        $this->assertDatabaseCount('users', 1);
        $this->assertGuest();
    }

    public function test_registration_requires_real_turnstile_verification_when_configured(): void
    {
        config(['services.turnstile.site_key' => 'site', 'services.turnstile.secret' => 'secret']);
        Http::fake(['challenges.cloudflare.com/*' => Http::sequence()->push(['success' => false])->push(['success' => true])]);
        $this->post('/register', $this->data())->assertSessionHasErrors('cf-turnstile-response');
        $this->post('/register', $this->data(['cf-turnstile-response' => 'bad']))->assertSessionHasErrors('cf-turnstile-response');
        $this->assertDatabaseCount('users', 0);
        $this->post('/register', $this->data(['cf-turnstile-response' => 'good']))->assertRedirect(route('home-page'));
        $this->assertDatabaseCount('users', 1);
    }

    public function test_google_redirect_keeps_two_recent_tabs_and_account_picker(): void
    {
        $r = $this->get('/auth/google')->assertRedirect();
        parse_str(parse_url($r->headers->get('Location'), PHP_URL_QUERY), $first);
        $this->assertSame('select_account', $first['prompt']);
        $this->assertSame('http://localhost/auth/google/callback', $first['redirect_uri']);
        Socialite::forgetDrivers();
        $r = $this->get('/auth/google')->assertRedirect();
        parse_str(parse_url($r->headers->get('Location'), PHP_URL_QUERY), $second);
        $states = session('google_oauth_states');
        $this->assertArrayHasKey($first['state'], $states);
        $this->assertArrayHasKey($second['state'], $states);
        $this->assertNotSame($first['state'], $second['state']);
    }

    public function test_google_rejects_missing_expired_or_replayed_state(): void
    {
        $this->get('/auth/google/callback?code=x&state=untrusted')->assertRedirect(route('login'))->assertSessionHasErrors('google');
        $this->withSession(['google_oauth_states' => ['old' => time() - 601]])->get('/auth/google/callback?state=old&code=x')->assertSessionHasErrors('google');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_google_valid_identity_creates_player_and_rotates_login(): void
    {
        $this->provider();
        $this->googleCallback()->assertRedirect(route('home-page'));
        $this->assertSame('google-123', User::firstOrFail()->google_id);
        $this->assertFalse(User::firstOrFail()->is_admin);
        $this->assertAuthenticatedAs(User::firstOrFail());
        $this->assertEmpty(session('google_oauth_states'));
    }

    public function test_google_existing_identity_logs_into_same_user(): void
    {
        $user = User::factory()->create();
        $user->google_id = 'google-123';
        $user->save();
        $this->provider();
        $this->googleCallback()->assertRedirect(route('home-page'));
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_google_email_collision_requires_existing_password_before_linking(): void
    {
        $user = User::factory()->create(['email' => 'baru@example.test', 'password' => 'ExistingPass2026!']);
        $this->provider();
        $this->googleCallback()->assertRedirect(route('google.link'));
        $this->assertGuest();
        $this->assertNull($user->fresh()->google_id);
        $this->get('/auth/google/link')->assertOk()->assertSee('HUBUNGKAN GOOGLE');
        $this->from('/auth/google/link')->post('/auth/google/link', ['password' => 'wrong'])->assertSessionHasErrors('password');
        $this->assertNull($user->fresh()->google_id);
        $this->post('/auth/google/link', ['password' => 'ExistingPass2026!'])->assertRedirect(route('home-page'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame('google-123', $user->fresh()->google_id);
        $this->assertTrue(Hash::check('ExistingPass2026!', $user->fresh()->password));
    }

    public function test_google_link_expiry_and_unverified_identity_are_rejected(): void
    {
        $this->withSession(['google_link' => ['expires_at' => time() - 1]])->get('/auth/google/link')->assertRedirect(route('login'));
        $this->provider($this->identity(verified: false));
        $this->googleCallback()->assertSessionHasErrors('google');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_google_cancel_and_connection_failure_return_actionable_errors(): void
    {
        $this->withSession(['google_oauth_states' => ['cancel-state' => time()]])->get('/auth/google/callback?state=cancel-state&error=access_denied')->assertSessionHasErrors('google');
        $this->provider(exception: new ConnectException('timeout', new HttpRequest('GET', 'https://www.googleapis.com')));
        $this->googleCallback()->assertRedirect(route('login'))->assertSessionHasErrors('google');
        $this->assertStringContainsString('menghubungi Google', session('errors')->first('google'));
        $this->assertGuest();
    }

    public function test_google_used_code_and_state_cannot_be_replayed_after_logout(): void
    {
        $this->provider();
        $this->googleCallback()->assertRedirect(route('home-page'));
        $this->post('/logout');
        $this->get('/auth/google/callback?state=good-state&code=example-code')->assertRedirect(route('login'))->assertSessionHasErrors('google');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_google_expired_code_returns_retry_message_without_creating_user(): void
    {
        $this->provider(exception: new RequestException('failed', new HttpRequest('POST', 'https://www.googleapis.com/oauth2/v4/token'), new Response(400, [], json_encode(['error' => 'invalid_grant']))));
        $this->googleCallback()->assertRedirect(route('login'))->assertSessionHasErrors('google');
        $this->assertStringContainsString('kedaluwarsa atau sudah dipakai', session('errors')->first('google'));
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }
}
