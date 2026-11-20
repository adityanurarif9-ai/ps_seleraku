<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    public function redirect()
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('login')->withErrors([
                'google' => 'Login Google belum diaktifkan oleh admin. Gunakan akun PS Seleraku atau pilih akun demo di bawah.',
            ]);
        }

        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('login')->withErrors(['google' => 'Login Google dibatalkan. Silakan coba lagi.']);
        }

        try {
            $identity = Socialite::driver('google')->user();
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('login')->withErrors(['google' => 'Login Google belum berhasil. Mulai lagi dari tombol Google.']);
        }

        $email = Str::lower($identity->getEmail() ?? '');
        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || ($identity->user['email_verified'] ?? false) !== true) {
            return redirect()->route('login')->withErrors(['google' => 'Gunakan akun Google dengan email yang sudah terverifikasi.']);
        }

        $user = User::where('google_id', $identity->getId())->first();

        if (! $user) {
            if (User::where('email', $email)->exists()) {
                return redirect()->route('login')->withErrors([
                    'google' => 'Email ini sudah memiliki akun PS Seleraku. Masuk dengan kata sandi akun tersebut.',
                ]);
            }

            $user = new User;
            $user->name = $identity->getName() ?: 'Pemain';
            $user->email = $email;
            $user->password = Str::random(64);
            $user->google_id = $identity->getId();
            $user->email_verified_at = now();
            $user->is_admin = false;
            $user->save();
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('home-page'));
    }
};