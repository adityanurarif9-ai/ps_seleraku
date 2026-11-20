<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a0d25">
    <title>Masuk · PS Seleraku</title>
    <link rel="stylesheet" href="{{ asset('login.css') }}?v=clean-1">
    <script src="{{ asset('login.js') }}?v=3" defer></script>
    @if(config('services.turnstile.site_key'))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onTurnstileLoad&amp;render=explicit" defer></script>
    @endif
</head>
<body>
    <main class="login-page">
        <section class="intro" aria-labelledby="intro-title">
            <div class="intro-content">
                <a class="brand" href="{{ route('login') }}" aria-label="PS Seleraku">
                    <img src="{{ asset('logo-ps-seleraku-transparent.png') }}" alt="PS Seleraku — Rental Konsol" width="1536" height="1024">
                </a>
                <h1 id="intro-title">ELEVATE YOUR<br> <span>GAMING</span><br> EXPERIENCE</h1>
                <p>Masuk ke akunmu sekarang dan nikmati pengalaman rental PlayStation terbaik dengan fasilitas higienis prima, dan harga kompetitif.</p>
            </div>
        </section>
        <section class="form-side" aria-labelledby="login-title">
            <div class="login-card">
                <h2 id="login-title">MASUK AKUN</h2>
                <p class="subtitle">Lanjutkan untuk mulai merental PS favoritmu</p>
                @if(session('session_notice'))
                    <p class="notice" role="status">{{ session('session_notice') }}</p>
                @endif
                @if($errors->any())
                    <p class="error" id="login-error" role="alert">{{ $errors->first() }}</p>
                @endif
                <form action="{{ route('login') }}" method="post" class="login-form">
                    @csrf
                    <label for="email">EMAIL</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email anda" autocomplete="username" maxlength="255" required>
                    <div class="password-label">
                        <label for="password">PASSWORD</label>
                        <button type="button" class="text-button" data-open-dialog="password-help">LUPA PASSWORD?</button>
                    </div>
                    <div class="password-field">
                        <input id="password" type="password" name="password" placeholder="••••••••" autocomplete="current-password" maxlength="255" required>
                        <button type="button" class="password-toggle" aria-controls="password" aria-pressed="false" aria-label="Tampilkan kata sandi"><span class="eye" aria-hidden="true"></span></button>
                    </div>
                    <label class="remember"><input type="checkbox" name="remember" value="1" @checked(old('remember'))><span>Ingat saya di perangkat ini</span></label>
                    @if(config('services.turnstile.site_key'))
                        <div class="verification">
                            <div id="turnstile-widget" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                            <p id="verification-status" role="status">Menyiapkan verifikasi Cloudflare…</p>
                            <button type="button" class="text-button" id="verification-retry" hidden>Coba verifikasi lagi</button>
                            <noscript>Aktifkan JavaScript untuk memverifikasi Cloudflare.</noscript>
                        </div>
                    @endif
                    <button type="submit" class="submit" @disabled(config('services.turnstile.site_key'))>MASUK SEKARANG</button>
                </form>
                <div class="divider"><span>ATAU</span></div>
                @if(config('services.google.client_id') && config('services.google.client_secret'))
                    <a class="google-button" href="{{ route('google.redirect') }}"><span class="google-icon" aria-hidden="true">G</span> Masuk dengan Google</a>
                @else
                    <button class="google-button" type="button" data-open-dialog="google-help"><span class="google-icon" aria-hidden="true">G</span> Masuk dengan Google</button>
                @endif
                <p class="signup">Belum punya akun? <button type="button" data-open-dialog="register-help">Daftar Disini</button></p>
                @if(config('app.demo_accounts'))
                    <details class="demo" open>
                        <summary>Coba akun demo</summary>
                        <div class="demo-buttons">
                            <button type="button" data-login="pemain" data-password="Mabar!2026">Akun pemain <span aria-hidden="true">↗</span></button>
                            <button type="button" data-login="admin" data-password="Seleraku!2026">Akun admin <span aria-hidden="true">↗</span></button>
                        </div>
                    </details>
                @endif
            </div>
        </section>
    </main>
    <dialog id="password-help">
        <h2>Lupa kata sandi?</h2>
        <p>Untuk akun lokal, hubungi admin PS Seleraku agar dibantu mengakses akun. Jika mendaftar dengan Google, gunakan tombol Masuk dengan Google.</p>
        <button type="button" class="submit" data-close-dialog>KEMBALI KE LOGIN</button>
    </dialog>
    <dialog id="register-help">
        <h2>Daftar PS Seleraku</h2>
        <p>Gunakan akun Google untuk membuat akun pemain saat login pertama. Pendaftaran Google tersedia setelah OAuth Google diaktifkan oleh admin.</p>
        <a class="google-button" href="{{ route('google.redirect') }}"><span class="google-icon" aria-hidden="true">G</span> Lanjutkan dengan Google</a>
        <button type="button" class="text-button dialog-close" data-close-dialog>Kembali</button>
    </dialog>
    <dialog id="google-help">
        <h2>Login Google belum aktif</h2>
        <p>Admin perlu menghubungkan PS Seleraku ke Google terlebih dahulu. Untuk mencoba website sekarang, pilih <strong>Akun pemain</strong> atau <strong>Akun admin</strong> di bagian akun demo, lalu tekan <strong>Masuk Sekarang</strong>.</p>
        <button type="button" class="submit" data-close-dialog>KEMBALI KE LOGIN</button>
    </dialog>
</body>
</html>
