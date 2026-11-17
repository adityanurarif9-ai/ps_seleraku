<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $siteKey = config('services.turnstile.site_key');
        $secret = config('services.turnstile.secret');
        if ($siteKey || $secret) {
            $request->validate(['cf-turnstile-response' => ['required', 'string', 'max:2048']], [
                'cf-turnstile-response.required' => 'Selesaikan verifikasi Cloudflare terlebih dahulu.',
            ]);

            try {
                $verified = $siteKey && $secret && Http::asForm()->timeout(5)
                    ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                        'secret' => $secret,
                        'response' => $request->input('cf-turnstile-response'),
                    ])->json('success') === true;
            } catch (\Illuminate\Http\Client\ConnectionException) {
                $verified = false;
            }

            if (! $verified) {
                throw ValidationException::withMessages([
                    'cf-turnstile-response' => 'Verifikasi Cloudflare gagal. Coba lagi.',
                ]);
            }
        }

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi salah.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('catalog'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
