<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TurnstileVerification
{
    public function validate(Request $request): void
    {
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
                    'cf-turnstile-response' => 'Server belum bisa menghubungi Cloudflare. Coba kirim formulir lagi.',
                ]);
            }

            if (! $verification->successful() || $verification->json('success') !== true) {
                $codes = $verification->json('error-codes', []);
                Log::warning('Turnstile validation rejected', ['status' => $verification->status(), 'codes' => $codes]);
                $message = match (true) {
                    $verification->serverError() => 'Layanan Cloudflare sedang bermasalah. Coba kirim formulir lagi.',
                    is_array($codes) && in_array('timeout-or-duplicate', $codes, true) => 'Verifikasi Cloudflare kedaluwarsa atau sudah dipakai. Coba kirim formulir lagi.',
                    default => 'Token verifikasi Cloudflare ditolak. Coba kirim formulir lagi.',
                };

                throw ValidationException::withMessages(['cf-turnstile-response' => $message]);
            }
        }

    }
}
