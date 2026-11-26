@extends('site.layout', ['activePage'=>'home-page'])
@section('title', __('Beranda'))
@section('content')
<section class="web-home-hero">
<div class="web-wrap web-hero-grid">
<div>
    <span class="web-eyebrow">{{ __('PILIH KONSOL. AJAK TEMAN MAIN.') }}</span>
    <h1>{{ $site['hero_title'] }}</h1>
<p class="web-lead">{{ $site['hero_description'] }}</p>
    <div class="web-actions">
<a class="web-button" href="{{ route('reservations.create') }}">{{ __('Buat reservasi') }} <span>↗</span>
</a>
<a class="web-button web-button-outline" href="{{ route('page-ke2') }}">{{ __('Lihat konsol') }}</a>
</div>
    <div class="web-hero-meta">
<span>PlayStation &amp; Nintendo</span>
<span>{{ __('Jadwal sesuai pilihanmu') }}</span>
</div>
</div>
<div class="web-hero-art">
<img src="{{ \App\Support\SiteAsset::url('hero.png') }}" alt="Konsol dan stik PlayStation untuk waktu bermain bersama" width="720" height="720" fetchpriority="high" decoding="async">
<div class="web-art-caption">
<span>{{ __('Waktunya main bareng.') }}</span>
<small>{{ __('Cek jadwal, pesan, lalu nikmati sesimu.') }}</small>
</div>
</div>
</div>
</section>
<section class="web-section web-wrap">
<div class="web-section-top">
<div>
<span class="web-eyebrow">{{ __('PILIHAN MAINMU') }}</span>
<h2>{{ __('Konsol favorit, jadwal yang pas.') }}</h2>
</div>
<a class="web-text-link" href="{{ route('page-ke2') }}">{{ __('Semua konsol & game ↗') }}</a>
</div>
<div class="web-console-grid">@forelse(array_merge($proUnits, $classicUnits) as $unit)<article class="web-console-card">
<a href="{{ route('reservations.create') }}">
<img src="{{ \App\Support\SiteAsset::url($unit['image']) }}" alt="{{ $unit['name'] }}" width="460" height="300" loading="lazy" decoding="async">
</a>
<div class="web-console-info">
<span class="web-availability">{{ $unit['statusLabel'] }}</span>
<h3>{{ $unit['name'] }}</h3>
<p>
<strong>{{ $unit['rateText'] }}</strong> {{ __('/ jam') }}</p>
<a class="web-text-link" href="{{ route('reservations.create') }}">{{ __('Pilih jadwal ↗') }}</a>
</div>
</article>@empty<p>{{ __('Pengelola sedang menyiapkan pilihan konsol.') }}</p>@endforelse</div>
</section>
<section class="web-soft">
<div class="web-wrap web-section web-feature-grid">
<div>
<span class="web-eyebrow">{{ __('DARI RENCANA JADI MABAR') }}</span>
<h2>{{ __('Pesan dulu.') }}<br>{{ __('Main lebih tenang.') }}</h2>
<p>{{ __('Cukup satu akun untuk mengatur rencana main, melihat konfirmasi, dan memantau sisa waktu.') }}</p>
<a class="web-text-link" href="{{ route('cara-sewa') }}">{{ __('Pelajari cara sewa ↗') }}</a>
</div>
<ol class="web-step-list">
<li>
<span>01</span>
<div>
<h3>{{ __('Pilih konsol dan jadwal') }}</h3>
<p>{{ __('Tentukan tanggal dan durasi. Perkiraan biaya tampil sebelum reservasi dikirim.') }}</p>
</div>
</li>
<li>
<span>02</span>
<div>
<h3>{{ __('Tunggu konfirmasi pengelola') }}</h3>
<p>{{ __('Pantau pesanan melalui halaman Rental Saya.') }}</p>
</div>
</li>
<li>
<span>03</span>
<div>
<h3>{{ __('Nikmati waktu bermain') }}</h3>
<p>{{ __('Lihat sisa waktu dan ajukan tambahan waktu dari akunmu.') }}</p>
</div>
</li>
</ol>
</div>
</section>
<section class="web-wrap web-section">
<div class="web-section-top">
<div>
<span class="web-eyebrow">{{ __('BAWA SERUNYA KE RUMAH') }}</span>
<h2>{{ __('Pilihan sewa harian.') }}</h2>
</div>
<a class="web-text-link" href="{{ route('sewa-harian') }}">{{ __('Lihat semua paket ↗') }}</a>
</div>
<div class="web-package-grid">@forelse($packages as $pkg)<article class="web-package">
<small>{{ __('24 JAM') }}</small>
<h3>{{ $pkg['name'] }}</h3>
<p>{{ $pkg['desc'] }}</p>
<strong>{{ $pkg['price'] }} <small>{{ __('/ 24 jam') }}</small>
</strong>
<a class="web-text-link" href="{{ route('reservations.create') }}">{{ __('Cek jadwal ↗') }}</a>
</article>@empty<p>{{ __('Belum ada paket harian yang ditampilkan.') }}</p>@endforelse</div>
</section>
<section class="web-wrap web-section web-about-strip">
<div>
<span class="web-eyebrow">{{ __('KENALAN LEBIH DEKAT') }}</span>
<h2>{{ $site['about_title'] }}</h2>
<p>{{ \Illuminate\Support\Str::limit($site['about_body'], 220) }}</p>
</div>
<div class="web-actions">
<a class="web-button" href="{{ route('tentang') }}">{{ __('Tentang kami ↗') }}</a>
<a class="web-text-link" href="{{ route('kontak') }}">{{ __('Hubungi pengelola') }}</a>
</div>
</section>
@endsection
