@php($site = $site ?? \App\Models\SiteContent::localizedValues())
<footer id="kontak" class="web-footer">
<div class="web-wrap">
<div class="web-footer-invite"><div><small>PS SELERAKU</small><h2>{{ __('Jadwalkan main.') }}<br>{{ __('Bawa pulang cerita.') }}</h2></div><a class="web-footer-book" href="{{ route('reservations.create') }}">{{ __('Buat reservasi') }}</a></div>
<div class="web-footer-grid">
<div>
<a href="{{ route('home-page') }}">
<img src="{{ \App\Support\SiteAsset::url('logo-ps-seleraku-transparent.png') }}" alt="PS Seleraku" width="220" height="74" loading="lazy" decoding="async">
</a>
<p>{{ __('Pilih konsol, rencanakan waktu main, dan pantau rental dalam satu akun.') }}</p>
<a class="web-footer-cta" href="{{ route('reservations.create') }}">{{ __('Rencanakan mainmu ↗') }}</a>
</div>
<div>
<h3>{{ __('Jelajahi') }}</h3>
<a href="{{ route('tentang') }}">{{ __('Tentang kami') }}</a>
<a href="{{ route('page-ke2') }}">{{ __('Konsol & Game') }}</a>
<a href="{{ route('sewa-harian') }}">{{ __('Sewa Harian') }}</a>
<a href="{{ route('menu-fnb') }}">{{ __('Menu & Kedai') }}</a>
</div>
<div>
<h3>{{ __('Bantuan') }}</h3>
<a href="{{ route('cara-sewa') }}">{{ __('Cara sewa') }}</a>
<a href="{{ route('reservations.index') }}">{{ __('Rental Saya') }}</a>
<a href="{{ route('kontak') }}">{{ __('Kontak pengelola') }}</a>
<p>{{ $site['opening_hours'] ?: __('Jam operasional akan diinformasikan pengelola.') }}</p>
</div>
<div>
<h3>{{ __('Kunjungi kami') }}</h3>
<p class="web-prose">{{ $site['address'] ?: __('Alamat akan diinformasikan pengelola.') }}</p>
<div class="web-footer-map"><iframe title="{{ __('Peta patokan SMK Telkom Purwokerto') }}" src="https://maps.google.com/maps?q=SMK%20Telkom%20Purwokerto&amp;z=15&amp;output=embed" width="320" height="160" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe><a href="https://www.google.com/maps/search/?api=1&amp;query=SMK%20Telkom%20Purwokerto" target="_blank" rel="noopener noreferrer">{{ __('Buka Google Maps') }} ↗</a><small>{{ __('Patokan area: SMK Telkom Purwokerto. Bukan alamat toko.') }}</small></div>
@if($site['address'])<a class="web-map-link" href="https://www.google.com/maps/search/?api=1&amp;query={{ rawurlencode($site['address']) }}" target="_blank" rel="noopener">{{ __('Lihat lokasi di peta') }}</a>@endif@if($site['whatsapp'])<a href="{{ \App\Models\SiteContent::whatsappUrl($site['whatsapp']) }}" target="_blank" rel="noopener">WhatsApp ↗</a>@endif @if($site['contact_email'])<a href="mailto:{{ $site['contact_email'] }}">{{ $site['contact_email'] }}</a>@endif</div>
</div>
<div class="web-footer-bottom">
<span>© {{ date('Y') }} PS Seleraku</span>
<div class="web-footer-links"><a href="{{ route('cara-sewa') }}">{{ __('Panduan & ketentuan sewa') }}</a><a href="{{ route('kontak') }}">{{ __('Hubungi kami') }}</a></div>
</div>
</div>
</footer>
