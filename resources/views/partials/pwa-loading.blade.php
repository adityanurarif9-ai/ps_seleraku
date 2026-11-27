<div class="app-splash" role="status" aria-live="polite"><div class="app-welcome"><img src="{{ \App\Support\SiteAsset::url('logo-ps-seleraku-transparent.png') }}" alt="PS Seleraku" width="640" height="213" decoding="async"><p class="app-welcome-eyebrow">{{ __('WAKTUNYA MAIN') }}</p><h1>{{ __('Selamat datang di') }}<br><strong>PS Seleraku.</strong></h1><p>{{ __('Pilih konsol, pesan jadwal, dan nikmati waktu mainmu.') }}</p><span class="app-welcome-track" aria-hidden="true"><i></i></span></div></div>
<div class="page-loading" hidden role="status" aria-label="{{ __('Memuat halaman') }}"></div>

@include('partials.selo')

@if(request()->routeIs('login', 'register', 'google.link') || request()->is('/'))
<div class="ps-language-auth">@include('partials.language-switch')</div>
@endif
