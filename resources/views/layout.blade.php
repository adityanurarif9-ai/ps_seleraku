<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title') · PS Seleraku</title>
<link rel="stylesheet" href="{{ asset('seleraku.css') }}">
<script src="{{ asset('seleraku.js') }}" defer></script>
@stack('head')
</head>
<body>
<header><div class="shell bar">
<a class="brand" href="{{ route('catalog') }}"><span aria-hidden="true">✚</span> PS Seleraku</a>
@auth
<nav aria-label="Akun">
@if(auth()->user()->is_admin)<a href="{{ url('/admin/catalog-items') }}">Panel admin ↗</a>@endif
<form action="{{ route('logout') }}" method="post">@csrf<button class="quiet">Keluar</button></form>
</nav>
@endauth
</div></header>
@if(session('session_notice'))
<div class="shell"><p class="error" role="status">{{ session('session_notice') }}</p></div>
@endif
@yield('content')
</body>
</html>