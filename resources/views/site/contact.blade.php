@extends('site.layout', ['activePage'=>'kontak'])
@section('title', __('Kontak'))
@section('content')
<section class="web-page-head">
<div class="web-wrap">
<span class="web-eyebrow">{{ __('KONTAK') }}</span>
<h1>{{ __('Yuk, bicarakan') }}<br>{{ __('rencana mainmu.') }}</h1>
<p class="web-lead">{{ __('Informasi pengelola dan operasional PS Seleraku.') }}</p>
</div>
</section>
<section class="web-wrap web-section web-contact-grid">
<div class="web-info-card">
<span>{{ __('01 · HUBUNGI KAMI') }}</span>
<h2>{{ __('Kontak pengelola.') }}</h2>@if($site['whatsapp'])<a class="web-contact-link" href="{{ \App\Models\SiteContent::whatsappUrl($site['whatsapp']) }}" target="_blank" rel="noopener">WhatsApp <strong>{{ $site['whatsapp'] }} ↗</strong>
</a>@endif @if($site['contact_email'])<a class="web-contact-link" href="mailto:{{ $site['contact_email'] }}">Email <strong>{{ $site['contact_email'] }}</strong>
</a>@endif @unless($site['whatsapp'] || $site['contact_email'])<p>{{ __('Kontak langsung belum ditambahkan pengelola. Kamu tetap bisa membuat reservasi dan memantau statusnya melalui akunmu.') }}</p>@endunless<a class="web-button" href="{{ route('reservations.create') }}">{{ __('Buat reservasi ↗') }}</a>
</div>
<div class="web-info-card">
<span>{{ __('02 · KUNJUNGAN') }}</span>
<h2>{{ __('Alamat dan jam buka.') }}</h2>
<h3>{{ __('Alamat') }}</h3>
<p class="web-prose">{{ $site['address'] ?: __('Alamat belum ditambahkan pengelola.') }}</p>
<h3>{{ __('Jam operasional') }}</h3>
<p>{{ $site['opening_hours'] ?: __('Jam operasional belum ditambahkan pengelola.') }}</p>@if($site['address'])<a class="web-text-link" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode($site['address']) }}">{{ __('Buka Google Maps ↗') }}</a>@endif</div>
</section>
@endsection
