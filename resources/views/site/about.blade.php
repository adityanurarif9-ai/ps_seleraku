@extends('site.layout', ['activePage'=>'tentang'])
@section('title', 'Tentang Kami')
@section('content')
<section class="web-page-head">
<div class="web-wrap">
<span class="web-eyebrow">TENTANG PS SELERAKU</span>
<h1>{{ $site['about_title'] }}</h1>
<p class="web-lead">Kenali tempat di balik waktu bermainmu.</p>
</div>
</section>
<section class="web-wrap web-section web-feature-grid">
<img class="web-about-image" src="{{ \App\Support\SiteAsset::url('hero.png') }}" alt="Pilihan konsol PlayStation PS Seleraku" width="640" height="640" decoding="async">
<div>
<h2>Lebih dekat dengan PS Seleraku.</h2>
<p class="web-prose">{{ $site['about_body'] }}</p>
<div class="web-actions">
<a class="web-button" href="{{ route('page-ke2') }}">Jelajahi konsol ↗</a>
<a class="web-text-link" href="{{ route('kontak') }}">Kontak pengelola</a>
</div>
</div>
</section>
<section class="web-soft">
<div class="web-wrap web-section">
<span class="web-eyebrow">SATU AKUN, SEMUA RENCANA MAIN</span>
<h2>Dibuat untuk memudahkan pelanggan.</h2>
<div class="web-three-grid">
<article class="web-info-card">
<span>01</span>
<h3>Reservasi sebelum datang</h3>
<p>Tentukan konsol, tanggal, dan durasi yang kamu inginkan.</p>
</article>
<article class="web-info-card">
<span>02</span>
<h3>Status yang bisa dipantau</h3>
<p>Lihat apakah pesananmu menunggu, dikonfirmasi, atau sedang berlangsung.</p>
</article>
<article class="web-info-card">
<span>03</span>
<h3>Waktu main dalam genggaman</h3>
<p>Pantau sesi dan ajukan perpanjangan jika masih ingin bermain.</p>
</article>
</div>
</div>
</section>
@endsection
