@php($site = $site ?? \App\Models\SiteContent::values())
<footer id="kontak" class="web-footer">
<div class="web-wrap">
<div class="web-footer-grid">
<div>
<a href="{{ route('home-page') }}">
<img src="{{ \App\Support\SiteAsset::url('logo-ps-seleraku-transparent.png') }}" alt="PS Seleraku" width="220" height="74" loading="lazy" decoding="async">
</a>
<p>Pilih konsol, rencanakan waktu main, dan pantau rental dalam satu akun.</p>
<a class="web-footer-cta" href="{{ route('reservations.create') }}">Rencanakan mainmu ↗</a>
</div>
<div>
<h3>Jelajahi</h3>
<a href="{{ route('tentang') }}">Tentang kami</a>
<a href="{{ route('page-ke2') }}">Konsol &amp; Game</a>
<a href="{{ route('sewa-harian') }}">Sewa Harian</a>
<a href="{{ route('menu-fnb') }}">Menu &amp; Kedai</a>
</div>
<div>
<h3>Bantuan</h3>
<a href="{{ route('cara-sewa') }}">Cara sewa</a>
<a href="{{ route('reservations.index') }}">Rental Saya</a>
<a href="{{ route('kontak') }}">Kontak pengelola</a>
<p>{{ $site['opening_hours'] ?: 'Jam operasional akan diinformasikan pengelola.' }}</p>
</div>
<div>
<h3>Kunjungi kami</h3>
<p class="web-prose">{{ $site['address'] ?: 'Alamat akan diinformasikan pengelola.' }}</p>@if($site['whatsapp'])<a href="{{ \App\Models\SiteContent::whatsappUrl($site['whatsapp']) }}" target="_blank" rel="noopener">WhatsApp ↗</a>@endif @if($site['contact_email'])<a href="mailto:{{ $site['contact_email'] }}">{{ $site['contact_email'] }}</a>@endif</div>
</div>
<div class="web-footer-bottom">
<span>© {{ date('Y') }} PS Seleraku</span>
<span>Waktu yang pas. Teman yang seru.</span>
</div>
</div>
</footer>
