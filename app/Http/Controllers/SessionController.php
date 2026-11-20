<?php

namespace App\Http\Controllers;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    public function store(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        if (config('app.demo_accounts') && User::query()->count() === 0) {
            app(DatabaseSeeder::class)->run();
        }

        $siteKey = config('services.turnstile.site_key');
        $secret = config('services.turnstile.secret');
        if ($siteKey || $secret) {
            $request->validate(['cf-turnstile-response' => ['required', 'string', 'max:2048']], [
                'cf-turnstile-response.required' => 'Selesaikan verifikasi Cloudflare terlebih dahulu.',
            ]);

            if (! $siteKey || ! $secret) {
                throw ValidationException::withMessages([
                    'cf-turnstile-response' => 'Konfigurasi verifikasi Cloudflare belum lengkap.',
                ]);
            }

            try {
                $verification = Http::asForm()
                    ->connectTimeout(6)
                    ->timeout(12)
                    ->retry([250], when: fn ($error) => $error instanceof ConnectionException, throw: false)
                    ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                        'secret' => $secret,
                        'response' => $request->input('cf-turnstile-response'),
                        'idempotency_key' => (string) Str::uuid(),
                    ]);
            } catch (ConnectionException $exception) {
                Log::warning('Turnstile connection failed', ['error' => $exception->getCode()]);
                throw ValidationException::withMessages([
                    'cf-turnstile-response' => 'Server belum bisa menghubungi Cloudflare. Coba masuk lagi.',
                ]);
            }

            if (! $verification->successful() || $verification->json('success') !== true) {
                $codes = $verification->json('error-codes', []);
                Log::warning('Turnstile validation rejected', ['status' => $verification->status(), 'codes' => $codes]);
                $message = match (true) {
                    $verification->serverError() => 'Layanan Cloudflare sedang bermasalah. Coba masuk lagi.',
                    is_array($codes) && in_array('timeout-or-duplicate', $codes, true) => 'Verifikasi Cloudflare kedaluwarsa atau sudah dipakai. Coba masuk lagi.',
                    default => 'Token verifikasi Cloudflare ditolak. Coba masuk lagi.',
                };

                throw ValidationException::withMessages(['cf-turnstile-response' => $message]);
            }
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi salah.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home-page'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
