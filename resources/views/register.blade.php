<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a0d25">
    <title>Daftar · PS Seleraku</title>
    <link rel="stylesheet" href="{{ asset('login.css') }}?v=auth-2">
    <script src="{{ asset('login.js') }}?v=auth-2" defer></script>
    @if(config('services.turnstile.site_key'))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onTurnstileLoad&amp;render=explicit" defer></script>
    @endif
</head>
<body>
    <main class="login-page">
        <section class="intro" aria-labelledby="intro-title">
            <div class="intro-content">
                <a class="brand" href="{{ route('login') }}" aria-label="PS Seleraku">
                    <img src="{{ asset('logo-ps-seleraku-transparent.png') }}" alt="PS SELERAKU — Rental Konsol" width="2172" height="724">
                </a>
                <h1 id="intro-title">ELEVATE YOUR<br> <span>GAMING</span><br> EXPERIENCE</h1>
                <p>Buat jadwal mainmu, pesan konsol favorit, dan pantau waktu rental dalam satu akun.</p>
            </div>
        </section>
        <section class="form-side" aria-labelledby="login-title">
            <div class="login-card">
                <h2 id="login-title">BUAT AKUN</h2>
                <p class="subtitle">Daftar untuk memesan konsol dan memantau waktu mainmu.</p>
                @if(session('session_notice'))
                    <p class="notice" role="status">{{ session('session_notice') }}</p>
                @endif
                @if($errors->any())
                    <p class="error" id="login-error" role="alert">{{ $errors->first() }}</p>
                @endif
                <form action="{{ route('register') }}" method="post" class="login-form" data-submit-label="DAFTAR SEKARANG">
                    @csrf
                    <label for="name">NAMA LENGKAP</label><input id="name" name="name" value="{{ old('name') }}" autocomplete="name" minlength="2" maxlength="100" placeholder="Nama lengkapmu" required>
                    <label for="email">EMAIL</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email anda" autocomplete="username" maxlength="255" required>
                    <label for="password">KATA SANDI</label>
                    <div class="password-field">
                        <input id="password" type="password" name="password" placeholder="••••••••" autocomplete="new-password" minlength="8" maxlength="255" required>
                        <button type="button" class="password-toggle" aria-controls="password" aria-pressed="false" aria-label="Tampilkan kata sandi"><span class="eye" aria-hidden="true"></span></button>
                    </div>
                    <p class="field-help" id="password-hint">Gunakan minimal 8 karakter.</p>
                    <label for="password_confirmation">ULANGI KATA SANDI</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" minlength="8" maxlength="255" placeholder="Ulangi kata sandimu" required>
                    @if(config('services.turnstile.site_key'))
                        <div class="verification">
                            <div id="turnstile-widget" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                            <p id="verification-status" role="status">Menyiapkan verifikasi Cloudflare…</p>
                            <button type="button" class="text-button" id="verification-retry" hidden>Coba verifikasi lagi</button>
                            <noscript>Aktifkan JavaScript untuk memverifikasi Cloudflare.</noscript>
                        </div>
                    @endif
                    <button type="submit" class="submit" @disabled(config('services.turnstile.site_key'))>DAFTAR SEKARANG</button>
                </form>
                <div class="divider"><span>ATAU</span></div>
                @if(config('services.google.client_id') && config('services.google.client_secret'))
                    <a class="google-button" href="{{ route('google.redirect') }}"><span class="google-icon" aria-hidden="true">G</span> Daftar dengan Google</a>
                @else
                    <button class="google-button" type="button" data-open-dialog="google-help"><span class="google-icon" aria-hidden="true">G</span> Masuk dengan Google</button>
                @endif
                <p class="signup">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>

            </div>
        </section>
    </main>
    <dialog id="google-help">
        <h2>Login Google belum aktif</h2>
        <p>Login Google belum tersedia. Kamu tetap bisa membuat akun menggunakan nama, email, dan kata sandi melalui formulir pendaftaran.</p>
        <button type="button" class="submit" data-close-dialog>KEMBALI KE LOGIN</button>
    </dialog>
</body>
</html>
