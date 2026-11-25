@extends('site.layout', ['activePage'=>'home-page'])
@section('title', 'Beranda')
@section('content')
<section class="web-home-hero">
<div class="web-wrap web-hero-grid">
<div>
    <span class="web-eyebrow">PILIH KONSOL. AJAK TEMAN MAIN.</span>
    <h1>{{ $site['hero_title'] }}</h1>
<p class="web-lead">{{ $site['hero_description'] }}</p>
    <div class="web-actions">
<a class="web-button" href="{{ route('reservations.create') }}">Buat reservasi <span>↗</span>
</a>
<a class="web-button web-button-outline" href="{{ route('page-ke2') }}">Lihat konsol</a>
</div>
    <div class="web-hero-meta">
<span>PlayStation &amp; Nintendo</span>
<span>Jadwal sesuai pilihanmu</span>
</div>
</div>
<div class="web-hero-art">
<img src="{{ \App\Support\SiteAsset::url('hero.png') }}" alt="Konsol dan stik PlayStation untuk waktu bermain bersama" width="720" height="720" fetchpriority="high" decoding="async">
<div class="web-art-caption">
<span>Waktunya main bareng.</span>
<small>Cek jadwal, pesan, lalu nikmati sesimu.</small>
</div>
</div>
</div>
</section>
<section class="web-section web-wrap">
<div class="web-section-top">
<div>
<span class="web-eyebrow">PILIHAN MAINMU</span>
<h2>Konsol favorit, jadwal yang pas.</h2>
</div>
<a class="web-text-link" href="{{ route('page-ke2') }}">Semua konsol &amp; game ↗</a>
</div>
<div class="web-console-grid">@forelse(array_merge($proUnits, $classicUnits) as $unit)<article class="web-console-card">
<a href="{{ route('reservations.create') }}">
<img src="{{ \App\Support\SiteAsset::url($unit['image']) }}" alt="{{ $unit['name'] }}" width="460" height="300" loading="lazy" decoding="async">
</a>
<div class="web-console-info">
<span class="web-availability">{{ $unit['statusLabel'] }}</span>
<h3>{{ $unit['name'] }}</h3>
<p>
<strong>{{ $unit['rateText'] }}</strong> / jam</p>
<a class="web-text-link" href="{{ route('reservations.create') }}">Pilih jadwal ↗</a>
</div>
</article>@empty<p>Pengelola sedang menyiapkan pilihan konsol.</p>@endforelse</div>
</section>
<section class="web-soft">
<div class="web-wrap web-section web-feature-grid">
<div>
<span class="web-eyebrow">DARI RENCANA JADI MABAR</span>
<h2>Pesan dulu.<br>Main lebih tenang.</h2>
<p>Cukup satu akun untuk mengatur rencana main, melihat konfirmasi, dan memantau sisa waktu.</p>
<a class="web-text-link" href="{{ route('cara-sewa') }}">Pelajari cara sewa ↗</a>
</div>
<ol class="web-step-list">
<li>
<span>01</span>
<div>
<h3>Pilih konsol dan jadwal</h3>
<p>Tentukan tanggal dan durasi. Perkiraan biaya tampil sebelum reservasi dikirim.</p>
</div>
</li>
<li>
<span>02</span>
<div>
<h3>Tunggu konfirmasi pengelola</h3>
<p>Pantau pesanan melalui halaman Rental Saya.</p>
</div>
</li>
<li>
<span>03</span>
<div>
<h3>Nikmati waktu bermain</h3>
<p>Lihat sisa waktu dan ajukan tambahan waktu dari akunmu.</p>
</div>
</li>
</ol>
</div>
</section>
<section class="web-wrap web-section">
<div class="web-section-top">
<div>
<span class="web-eyebrow">BAWA SERUNYA KE RUMAH</span>
<h2>Pilihan sewa harian.</h2>
</div>
<a class="web-text-link" href="{{ route('sewa-harian') }}">Lihat semua paket ↗</a>
</div>
<div class="web-package-grid">@forelse($packages as $pkg)<article class="web-package">
<small>24 JAM</small>
<h3>{{ $pkg['name'] }}</h3>
<p>{{ $pkg['desc'] }}</p>
<strong>{{ $pkg['price'] }} <small>/ 24 jam</small>
</strong>
<a class="web-text-link" href="{{ route('reservations.create') }}">Cek jadwal ↗</a>
</article>@empty<p>Belum ada paket harian yang ditampilkan.</p>@endforelse</div>
</section>
<section class="web-wrap web-section web-about-strip">
<div>
<span class="web-eyebrow">KENALAN LEBIH DEKAT</span>
<h2>{{ $site['about_title'] }}</h2>
<p>{{ \Illuminate\Support\Str::limit($site['about_body'], 220) }}</p>
</div>
<div class="web-actions">
<a class="web-button" href="{{ route('tentang') }}">Tentang kami ↗</a>
<a class="web-text-link" href="{{ route('kontak') }}">Hubungi pengelola</a>
</div>
</section>
@endsection
