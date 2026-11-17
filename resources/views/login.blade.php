@extends('layout')
@section('title', 'Masuk')
@if(config('services.turnstile.site_key'))
@push('head')
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endpush
@endif
@section('content')
<main class="shell login">
<section class="art"><p class="tag">RENTAL KONSOL &amp; KEDAI</p><h1>Waktunya <em>mabar.</em></h1></section>
<section class="panel"><h2>Masuk ke akunmu</h2><p>Masuk untuk melihat katalog PS Seleraku.</p>
@if($errors->any())<p class="error" role="alert">{{ $errors->first() }}</p>@endif
<form method="post" class="fields" action="{{ route('login') }}">
@csrf
<label>Email<input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required></label>
<label>Kata sandi<input id="password" name="password" type="password" autocomplete="current-password" required></label>
@if(config('services.turnstile.site_key'))
<div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="dark" data-size="flexible"></div>
@endif
<button class="primary" type="submit">Masuk →</button>
</form>
@if(config('app.demo_accounts'))
<div class="demo"><p>Pakai akun demo</p>
<button class="quiet" type="button" data-login="pemain" data-password="Mabar!2026">Pemain</button>
<button class="quiet" type="button" data-login="admin" data-password="Seleraku!2026">Admin</button>
</div>
@endif
</section>
</main>
@endsection