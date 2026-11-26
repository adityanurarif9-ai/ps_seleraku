<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0a0d25">
    <title>{{ __('Masuk · PS Seleraku') }}</title>
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
                <h1 id="intro-title">{{ __('Reservasi konsol.') }}<br><span>{{ __('Pilih waktu mainmu.') }}</span></h1>
                <p>{{ __('Pilih PlayStation atau Nintendo, pesan jadwal sebelum datang, lalu pantau reservasi dan sisa waktu main dari akunmu.') }}</p>
            </div>
        </section>
        <section class="form-side" aria-labelledby="login-title">
            <div class="auth-stack">
            @include('partials.install-app')
            <div class="login-card">
                <h2 id="login-title">{{ __('MASUK AKUN') }}</h2>
                <p class="subtitle">{{ __('Masuk untuk memesan konsol dan melihat rentalmu.') }}</p>
                @if(session('session_notice'))
                    <p class="notice" role="status">{{ session('session_notice') }}</p>
                @endif
                @if($errors->any())
                    <p class="error" id="login-error" role="alert">{{ $errors->first() }}</p>
                @endif
                <form action="{{ route('login') }}" method="post" class="login-form">
                    @csrf
                    <label for="email">EMAIL</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Masukkan email anda') }}" autocomplete="username" maxlength="255" required>
                    <div class="password-label">
                        <label for="password">{{ __('PASSWORD') }}</label>
                        <button type="button" class="text-button" data-open-dialog="password-help">{{ __('LUPA PASSWORD?') }}</button>
                    </div>
                    <div class="password-field">
                        <input id="password" type="password" name="password" placeholder="••••••••" autocomplete="current-password" maxlength="255" required>
                        <button type="button" class="password-toggle" aria-controls="password" aria-pressed="false" aria-label="{{ __('Tampilkan kata sandi') }}"><span class="eye" aria-hidden="true"></span></button>
                    </div>
                    <label class="remember"><input type="checkbox" name="remember" value="1" @checked(old('remember'))><span>{{ __('Ingat saya di perangkat ini') }}</span></label>
                    @if(config('services.turnstile.site_key'))
                        <div class="verification">
                            <div id="turnstile-widget" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                            <p id="verification-status" role="status">{{ __('Isi formulir untuk memulai verifikasi keamanan.') }}</p>
                            <button type="button" class="text-button" id="verification-start">{{ __('Mulai verifikasi') }}</button>
                            <button type="button" class="text-button" id="verification-retry" hidden>{{ __('Coba verifikasi lagi') }}</button>
                            <noscript>{{ __('Aktifkan JavaScript untuk memverifikasi Cloudflare.') }}</noscript>
                        </div>
                    @endif
                    <button type="submit" class="submit" @disabled(config('services.turnstile.site_key'))>{{ __('MASUK SEKARANG') }}</button>
                </form>
                <div class="divider"><span>{{ __('ATAU') }}</span></div>
                @if(config('services.google.client_id') && config('services.google.client_secret'))
                    <a class="google-button" href="{{ route('google.redirect') }}"><span class="google-icon" aria-hidden="true">G</span> {{ __('Masuk dengan Google') }}</a>
                @else
                    <button class="google-button" type="button" data-open-dialog="google-help"><span class="google-icon" aria-hidden="true">G</span> {{ __('Masuk dengan Google') }}</button>
                @endif
                <p class="signup"><a href="{{ route('tentang') }}">{{ __('Tentang PS Seleraku') }}</a> · <a href="{{ route('kontak') }}">{{ __('Kontak') }}</a></p>
                <p class="signup">{{ __('Belum punya akun?') }} <a href="{{ route('register') }}">{{ __('Daftar di sini') }}</a></p>
                <p class="signup admin-entry">{{ __('Mengelola PS Seleraku?') }} <a href="{{ route('filament.admin.auth.login') }}">{{ __('Masuk admin') }}</a></p>
                @if(config('app.demo_accounts'))
                    <details class="demo" open>
                        <summary>{{ __('Coba akun demo') }}</summary>
                        <div class="demo-buttons">
                            <button type="button" data-login="pemain" data-password="Mabar!2026">{{ __('Akun pemain') }} <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></button>
                            <button type="button" data-login="admin" data-password="Seleraku!2026">{{ __('Akun admin') }} <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></button>
                        </div>
                    </details>
                @endif
            </div>
            </div>
        </section>
    </main>
    <dialog id="password-help">
        <h2>{{ __('Lupa kata sandi?') }}</h2>
        <p>{{ __('Untuk akun lokal, hubungi admin PS Seleraku agar dibantu mengakses akun. Jika mendaftar dengan Google, gunakan tombol Masuk dengan Google.') }}</p>
        <button type="button" class="submit" data-close-dialog>{{ __('KEMBALI KE LOGIN') }}</button>
    </dialog>
    <dialog id="google-help">
        <h2>{{ __('Login Google belum aktif') }}</h2>
        <p>Admin perlu menghubungkan PS Seleraku ke Google terlebih dahulu. Untuk mencoba website sekarang, pilih <strong>{{ __('Akun pemain') }}</strong> {{ __('atau') }} <strong>{{ __('Akun admin') }}</strong> di bagian akun demo, lalu tekan <strong>{{ __('Masuk Sekarang') }}</strong>.</p>
        <button type="button" class="submit" data-close-dialog>{{ __('KEMBALI KE LOGIN') }}</button>
    </dialog>
</body>
</html>
