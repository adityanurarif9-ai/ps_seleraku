<meta name="description" content="{{ __('Reservasi konsol PlayStation dan Nintendo di PS Seleraku. Pilih jadwal, lihat ketersediaan, dan pantau waktu rental dari akunmu.') }}">
@if($authPage ?? false)
<style>{!! \App\Support\SiteAsset::styles('fonts/poppins-v24.css') !!}{!! \App\Support\SiteAsset::styles('login.css') !!}{!! \App\Support\SiteAsset::styles('pwa.css') !!}</style>
@foreach([400, 600, 700, 800] as $weight)
<link rel="preload" href="{{ asset('fonts/poppins-v24-'.$weight.'-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
@endforeach
<link rel="preload" href="{{ \App\Support\SiteAsset::url('fonts/poppins-v24-900-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
<link rel="preload" as="image" href="{{ asset(config('performance-assets.login.mobile')) }}" media="(max-width: 700px)" type="image/webp" fetchpriority="high">
<link rel="preload" as="image" href="{{ asset(config('performance-assets.login.desktop')) }}" media="(min-width: 701px)" type="image/webp" fetchpriority="high">
@else
<link rel="stylesheet" href="{{ \App\Support\SiteAsset::url('fonts/poppins-v24.css') }}">
@endif
