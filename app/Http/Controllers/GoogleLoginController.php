<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleLoginController extends Controller
{
    private function failed(string $message)
    {
        return redirect()->route('login')->withErrors(['google' => $message]);
    }

    public function redirect(Request $request)
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return $this->failed('Login Google belum diaktifkan. Kamu tetap bisa daftar dan masuk dengan email serta kata sandi.');
        }
        $request->session()->forget('google_link');
        $response = Socialite::driver('google')->with(['prompt' => 'select_account'])->redirect();
        // Keep a short, bounded list so opening another login tab does not invalidate the first.
        $states = array_filter($request->session()->get('google_oauth_states', []), fn ($issued) => $issued >= time() - 600);
        $states[$request->session()->get('state')] = time();
        $request->session()->put('google_oauth_states', array_slice($states, -5, null, true));

        return $response->header('Cache-Control', 'no-store, private');
    }

    public function callback(Request $request)
    {
        $state = $request->query('state');
        $states = $request->session()->get('google_oauth_states', []);
        $issued = is_string($state) ? ($states[$state] ?? null) : null;
        if (! $issued || $issued < time() - 600) {
            return $this->failed('Sesi login Google kedaluwarsa atau tidak cocok. Mulai lagi dari tombol Google pada browser yang sama.');
        }
        unset($states[$state]);
        $request->session()->put('google_oauth_states', $states);
        if ($request->has('error')) {
            return $this->failed('Login Google dibatalkan. Kamu bisa mencoba lagi atau masuk dengan email dan kata sandi.');
        }
        if (! is_string($request->query('code')) || $request->query('code') === '') {
            return $this->failed('Tautan login Google tidak lengkap. Mulai lagi dari tombol Google.');
        }
        // Socialite still validates state with hash_equals; it is never disabled.
        $request->session()->put('state', $state);
        try {
            $identity = Socialite::driver('google')->setHttpClient(new Client(['connect_timeout' => 6, 'timeout' => 20]))->user();
        } catch (InvalidStateException $exception) {
            return $this->failed('Sesi login Google tidak cocok. Mulai lagi dari tombol Google pada browser yang sama.');
        } catch (ConnectException $exception) {
            Log::warning('Google sign-in connection failed', ['type' => get_class($exception)]);

            return $this->failed('Server belum bisa menghubungi Google. Coba lagi sebentar atau masuk dengan email dan kata sandi.');
        } catch (RequestException $exception) {
            $error = json_decode((string) $exception->getResponse()?->getBody(), true)['error'] ?? null;
            Log::warning('Google sign-in provider rejected request', ['status' => $exception->getResponse()?->getStatusCode()]);

            return $this->failed(match ($error) {
                'invalid_grant' => 'Tautan login Google kedaluwarsa atau sudah dipakai. Mulai lagi dari tombol Google.',
                'redirect_uri_mismatch' => 'Alamat callback Google belum sesuai. Hubungi admin untuk memeriksa konfigurasi login Google.',
                'invalid_client' => 'Konfigurasi login Google perlu diperbarui oleh admin. Gunakan email dan kata sandi untuk sementara.',
                default => 'Google belum merespons dengan benar. Coba lagi atau masuk dengan email dan kata sandi.',
            });
        } catch (\Throwable $exception) {
            // Do not log OAuth codes, tokens or request bodies containing client credentials.
            Log::warning('Google sign-in failed', ['type' => get_class($exception)]);

            return $this->failed('Login Google belum berhasil. Coba lagi dari tombol Google atau gunakan email dan kata sandi.');
        }

        $email = Str::lower($identity->getEmail() ?? '');
        $id = (string) $identity->getId();
        if (! $id || strlen($id) > 255 || ! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255 || ($identity->user['email_verified'] ?? false) !== true) {
            return $this->failed('Gunakan akun Google dengan email yang sudah terverifikasi.');
        }

        try {
            $user = User::where('google_id', $id)->first();
            if (! $user) {
                $existing = User::where('email', $email)->first();
                if ($existing) {
                    if ($existing->google_id) {
                        return $this->failed('Email ini terhubung ke akun Google lain. Masuk dengan kata sandi akunmu.');
                    }
                    $request->session()->put('google_link', ['user_id' => $existing->id, 'email' => $email, 'google_id' => $id, 'expires_at' => time() + 300]);

                    return redirect()->route('google.link');
                }
                $user = new User;
                $user->name = mb_substr($identity->getName() ?: 'Pemain', 0, 100);
                $user->email = $email;
                $user->password = Str::random(64);
                $user->google_id = $id;
                $user->email_verified_at = now();
                $user->is_admin = false;
                $user->save();
            }
        } catch (UniqueConstraintViolationException $exception) {
            return $this->failed('Akun sedang diproses pada tab lain. Coba masuk kembali dari tombol Google.');
        }

        return $this->signedIn($request, $user);
    }

    private function pendingLink(Request $request): array
    {
        $link = $request->session()->get('google_link');
        if (! is_array($link) || ($link['expires_at'] ?? 0) < time()) {
            $request->session()->forget('google_link');
            throw ValidationException::withMessages(['google' => 'Konfirmasi Google kedaluwarsa. Mulai lagi dari tombol Google.'])->redirectTo(route('login'));
        }

        return $link;
    }

    public function linkForm(Request $request)
    {
        $link = $this->pendingLink($request);

        return response()->view('google-link', ['email' => $link['email']])->header('Cache-Control', 'no-store, private');
    }

    public function link(Request $request)
    {
        $link = $this->pendingLink($request);
        $request->validate(['password' => ['required', 'string', 'max:255']]);
        $user = DB::transaction(function () use ($request, $link) {
            $user = User::lockForUpdate()->findOrFail($link['user_id']);
            if ($user->email !== $link['email'] || $user->google_id || ! Hash::check($request->input('password'), $user->password)) {
                throw ValidationException::withMessages(['password' => 'Kata sandi belum cocok atau akun sudah terhubung. Coba masuk kembali.']);
            }
            if (User::where('google_id', $link['google_id'])->exists()) {
                throw ValidationException::withMessages(['password' => 'Akun Google ini sudah terhubung. Mulai lagi dari tombol Google.']);
            }
            $user->google_id = $link['google_id'];
            $user->email_verified_at = $user->email_verified_at ?? now();
            $user->save();

            return $user;
        });
        $request->session()->forget('google_link');

        return $this->signedIn($request, $user);
    }

    private function signedIn(Request $request, User $user)
    {
        Auth::login($user, remember: true);
        $request->session()->regenerate();
        $request->session()->forget(['google_oauth_states', 'state', 'google_link']);

        return redirect()->intended($user->is_admin ? url('/admin') : route('home-page'));
    }
}
