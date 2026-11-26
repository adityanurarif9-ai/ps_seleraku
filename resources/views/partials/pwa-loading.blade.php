<div class="app-splash" role="status" aria-live="polite"><div><img src="{{ \App\Support\SiteAsset::url('logo-ps-seleraku-transparent.png') }}" alt="PS Seleraku" width="2172" height="724" decoding="async" loading="lazy"><span class="app-splash-spinner" aria-hidden="true"></span><p>{{ __('Menyiapkan waktu mainmu…') }}</p></div></div>
<div class="page-loading" role="progressbar" aria-label="{{ __('Memuat halaman') }}" hidden></div>

@include('partials.selo')

@if(request()->routeIs('login', 'register', 'google.link') || request()->is('/'))
<div class="ps-language-auth">@include('partials.language-switch')</div>
@endif
