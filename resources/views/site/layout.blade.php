<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title') · PS Seleraku</title>
@include('partials.performance-head', ['authPage'=>false])
<link rel="stylesheet" href="{{ \App\Support\SiteAsset::url('seleraku-pages.css') }}">
<script src="{{ \App\Support\SiteAsset::url('seleraku-pages.js') }}" defer>
</script>
@include('partials.pwa-head')
</head>
<body class="ps-page-body web-body" data-reservation-url="{{ route('reservations.create') }}">
@include('partials.pwa-loading')
@include('partials.ps-navbar', ['activePage'=>$activePage ?? 'home-page'])
<main>@yield('content')</main>
@include('partials.ps-footer')
@include('partials.ps-modals')
</body>
</html>
