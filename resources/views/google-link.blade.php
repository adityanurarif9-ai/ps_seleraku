<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0a0d25">
    <title>Hubungkan Google · PS Seleraku</title>
    <link rel="stylesheet" href="{{ asset('login.css') }}?v=mobile-app-1">
    <script src="{{ asset('login.js') }}?v=auth-2" defer></script>

@include('partials.pwa-head')
</head>
<body>
@include('partials.pwa-loading')
    <main class="login-page">
        <section class="intro" aria-labelledby="intro-title">
            <div class="intro-content">
                <a class="brand" href="{{ route('login') }}" aria-label="PS Seleraku">
                    <img src="{{ asset('logo-ps-seleraku-transparent.png') }}" alt="PS SELERAKU — Rental Konsol" width="2172" height="724">
                </a>
                <h1 id="intro-title">ELEVATE YOUR <br> <span>GAMING</span> <br> EXPERIENCE</h1>
                <p>Buat jadwal mainmu, pesan konsol favorit, dan pantau waktu rental dalam satu akun.</p>
            </div>
        </section>
        <section class="form-side" aria-labelledby="login-title">
            <div class="login-card">
                <h2 id="login-title">HUBUNGKAN GOOGLE</h2>
                <p class="subtitle">Email <strong>{{ $email }}</strong> sudah memiliki akun PS Seleraku. Masukkan kata sandi akun tersebut sekali untuk menghubungkannya dengan Google.</p>
                @if($errors->any())<p class="error" role="alert">{{ $errors->first() }}</p>@endif
                <form class="login-form" action="{{ route('google.link') }}" method="post">@csrf
                    <label for="password">KATA SANDI PS SELERAKU</label>
                    <div class="password-field"><input id="password" type="password" name="password" autocomplete="current-password" maxlength="255" required><button type="button" class="password-toggle" aria-controls="password" aria-pressed="false" aria-label="Tampilkan kata sandi"><span class="eye" aria-hidden="true"></span></button></div>
                    <p class="field-help">Setelah terhubung, kamu bisa memakai tombol Google tanpa memasukkan kata sandi ini lagi.</p>
                    <button class="submit" type="submit">HUBUNGKAN DAN MASUK</button>
                </form>
                <p class="signup"><a href="{{ route('login') }}">Kembali ke login</a></p>
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
