<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0a0d25">
    <title>{{ __('Daftar · PS Seleraku') }}</title>
@include('partials.performance-head', ['authPage'=>true])
    <script src="{{ \App\Support\SiteAsset::url('login.js') }}?v=performance-1" defer></script>
@include('partials.pwa-head', ['authPage'=>true])
</head>
<body>
@include('partials.pwa-loading')
    <main class="login-page">
        <section class="intro" aria-labelledby="intro-title">
            <div class="intro-content">
                <a class="brand" href="{{ route('login') }}" aria-label="PS Seleraku">
                    <img srcset="{{ asset(config('performance-assets.brand.small')) }} 320w, {{ \App\Support\SiteAsset::url('logo-ps-seleraku-transparent.png') }} 640w" sizes="(max-width: 700px) 158px, 248px" src="{{ \App\Support\SiteAsset::url('logo-ps-seleraku-transparent.png') }}" alt="PS SELERAKU — Rental Konsol" width="2172" height="724" decoding="async">
                </a>
                <h1 id="intro-title">ELEVATE YOUR <br> <span>GAMING</span> <br> EXPERIENCE</h1>
                <p>{{ __('Buat jadwal mainmu, pesan konsol favorit, dan pantau waktu rental dalam satu akun.') }}</p>
            </div>
        </section>
        <section class="form-side" aria-labelledby="login-title">
            <div class="auth-stack">
            @include('partials.install-app')
            <div class="login-card">
                <h2 id="login-title">{{ __('BUAT AKUN') }}</h2>
                <p class="subtitle">{{ __('Daftar untuk memesan konsol dan memantau waktu mainmu.') }}</p>
                @if(session('session_notice'))
                    <p class="notice" role="status">{{ session('session_notice') }}</p>
                @endif
                @if($errors->any())
                    <p class="error" id="login-error" role="alert">{{ $errors->first() }}</p>
                @endif
                <form action="{{ route('register') }}" method="post" class="login-form" data-submit-label="DAFTAR SEKARANG">
                    @csrf
                    <label for="name">{{ __('NAMA LENGKAP') }}</label><input id="name" name="name" value="{{ old('name') }}" autocomplete="name" minlength="2" maxlength="100" placeholder="{{ __('Nama lengkapmu') }}" required>
                    <label for="email">EMAIL</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Masukkan email anda') }}" autocomplete="username" maxlength="255" required>
                    <label for="password">{{ __('KATA SANDI') }}</label>
                    <div class="password-field">
                        <input id="password" type="password" name="password" placeholder="••••••••" autocomplete="new-password" minlength="8" maxlength="255" required>
                        <button type="button" class="password-toggle" aria-controls="password" aria-pressed="false" aria-label="{{ __('Tampilkan kata sandi') }}"><span class="eye" aria-hidden="true"></span></button>
                    </div>
                    <p class="field-help" id="password-hint">{{ __('Gunakan minimal 8 karakter.') }}</p>
                    <label for="password_confirmation">{{ __('ULANGI KATA SANDI') }}</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" minlength="8" maxlength="255" placeholder="{{ __('Ulangi kata sandimu') }}" required>
                    @if(config('services.turnstile.site_key'))
                        <div class="verification">
                            <div id="turnstile-widget" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                            <p id="verification-status" role="status">{{ __('Isi formulir untuk memulai verifikasi keamanan.') }}</p>
                            <button type="button" class="text-button" id="verification-start">{{ __('Mulai verifikasi') }}</button>
                            <button type="button" class="text-button" id="verification-retry" hidden>{{ __('Coba verifikasi lagi') }}</button>
                            <noscript>{{ __('Aktifkan JavaScript untuk memverifikasi Cloudflare.') }}</noscript>
                        </div>
                    @endif
                    <button type="submit" class="submit" @disabled(config('services.turnstile.site_key'))>{{ __('DAFTAR SEKARANG') }}</button>
                </form>
                <div class="divider"><span>{{ __('ATAU') }}</span></div>
                @if(config('services.google.client_id') && config('services.google.client_secret'))
                    <a class="google-button" href="{{ route('google.redirect') }}"><span class="google-icon" aria-hidden="true">G</span> {{ __('Daftar dengan Google') }}</a>
                @else
                    <button class="google-button" type="button" data-open-dialog="google-help"><span class="google-icon" aria-hidden="true">G</span> {{ __('Masuk dengan Google') }}</button>
                @endif
                <p class="signup">{{ __('Sudah punya akun?') }} <a href="{{ route('login') }}">{{ __('Masuk di sini') }}</a></p>

            </div>
            </div>
        </section>
    </main>
    <dialog id="google-help">
        <h2>{{ __('Login Google belum aktif') }}</h2>
        <p>{{ __('Login Google belum tersedia. Kamu tetap bisa membuat akun menggunakan nama, email, dan kata sandi melalui formulir pendaftaran.') }}</p>
        <button type="button" class="submit" data-close-dialog>{{ __('KEMBALI KE LOGIN') }}</button>
    </dialog>
</body>
</html>
