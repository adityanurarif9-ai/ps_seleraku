<!doctype html>
<html lang="{{ app()->getLocale() }}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"><meta name="csrf-token" content="{{ csrf_token() }}"><title>@yield('title') · PS Seleraku</title>
@include('partials.performance-head', ['authPage'=>false])<link rel="stylesheet" href="{{ \App\Support\SiteAsset::url('seleraku-pages.css') }}?v=reservations-1"><link rel="stylesheet" href="{{ \App\Support\SiteAsset::url('rentals.css') }}?v=1"><script src="{{ \App\Support\SiteAsset::url('seleraku-pages.js') }}?v=reservations-1" defer></script><script src="{{ \App\Support\SiteAsset::url('rentals.js') }}?v=1" defer></script>@include('partials.pwa-head')
</head>
<body class="ps-page-body" data-reservation-url="{{ route('reservations.create') }}">
@include('partials.pwa-loading')
@include('partials.ps-navbar', ['activePage'=>'rentals'])
<main class="rs-shell">
@if(session('success'))<p class="rs-message" role="status">{{ session('success') }}</p>@endif
@if($errors->any())<div class="rs-errors" role="alert"><strong>{{ __('Periksa kembali') }}</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
</main>
@include('partials.ps-footer')
@include('partials.ps-modals')
</body></html>
