@extends('site.layout', ['activePage'=>'cara-sewa'])
@section('title', __('Cara Sewa'))
@section('content')
<section class="web-page-head">
<div class="web-wrap">
<span class="web-eyebrow">{{ __('CARA SEWA') }}</span>
<h1>{{ __('Dari pilih konsol') }}<br>{{ __('sampai mulai main.') }}</h1>
<p class="web-lead">{{ __('Ikuti langkahnya dan pantau semua proses melalui akunmu.') }}</p>
<a class="web-button" href="{{ route('reservations.create') }}">{{ __('Mulai reservasi ↗') }}</a>
</div>
</section>
<section class="web-wrap web-section web-feature-grid">
<ol class="web-step-list web-guide">@foreach([['Pilih konsol dan jenis sewa','Lihat katalog untuk tarif per jam atau paket sewa harian.'],['Tentukan jadwal dan durasi','Gunakan formulir reservasi untuk memeriksa ketersediaan dan perkiraan biaya.'],['Kirim dan pantau konfirmasi','Pesanan masuk ke pengelola. Status dikonfirmasi berarti jadwalmu telah disetujui.'],['Mulai sesi dan pantau waktu','Admin memulai sesi saat bermain dimulai. Sisa waktu tampil pada detail Rental Saya.'],['Ajukan tambahan waktu bila perlu','Tambahan waktu diproses setelah admin mengecek jadwal unit dan menyetujuinya.']] as $step)<li>
<span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
<div>
<h3>{{ __($step[0]) }}</h3>
<p>{{ __($step[1]) }}</p>
</div>
</li>@endforeach</ol>
<aside class="web-sticky-card">
<span class="web-eyebrow">{{ __('SEBELUM MEMESAN') }}</span>
<h2>{{ __('Catatan untuk pelanggan.') }}</h2>
<p class="web-prose">{{ $site['rental_terms'] }}</p>
<a class="web-text-link" href="{{ route('reservations.index') }}">{{ __('Buka Rental Saya ↗') }}</a>
<hr>
<p>{{ __('Ada yang belum jelas? Tanya Selo di pojok kanan bawah atau hubungi pengelola.') }}</p>
<a class="web-text-link" href="{{ route('kontak') }}">{{ __('Lihat kontak ↗') }}</a>
</aside>
</section>
@endsection
