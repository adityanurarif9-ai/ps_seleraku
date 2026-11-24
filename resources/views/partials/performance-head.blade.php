<meta name="description" content="Reservasi konsol PlayStation dan Nintendo di PS Seleraku. Pilih jadwal, lihat ketersediaan, dan pantau waktu rental dari akunmu.">
<link rel="stylesheet" href="{{ \App\Support\SiteAsset::url('fonts/poppins-v24.css') }}">
@if($authPage ?? false)
<link rel="preload" href="{{ \App\Support\SiteAsset::url('fonts/poppins-v24-900-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
<link rel="preload" as="image" href="{{ asset(config('performance-assets.login.mobile')) }}" media="(max-width: 700px)" type="image/webp" fetchpriority="high">
<link rel="preload" as="image" href="{{ asset(config('performance-assets.login.desktop')) }}" media="(min-width: 701px)" type="image/webp" fetchpriority="high">
@endif
