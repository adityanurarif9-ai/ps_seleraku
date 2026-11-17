@extends('layout')
@section('title', 'Katalog')
@section('content')
<main class="shell">
<section class="art hero"><p class="tag">RENTAL KONSOL &amp; KEDAI</p><h1>Waktunya <em>mabar.</em></h1><p>Halo, {{ auth()->user()->name }}! Pilih konsol dan camilan teman mainmu.</p><a class="primary" href="#konsol">Lihat konsol ↓</a></section>
<section class="section" id="konsol"><h2>Pilih konsolmu</h2><div class="cards">
@forelse($consoles as $item)
<article class="card"><div class="console-art" aria-hidden="true">✚ <span>••</span></div><p class="stock">{{ $item->stock ? $item->stock.' unit tersedia' : 'Tidak tersedia' }}</p><h3>{{ $item->name }}</h3><p class="price">Rp{{ number_format($item->price, 0, ',', '.') }} <small>/ jam</small></p>
@if($item->stock)<button class="quiet" type="button" data-unit="{{ $item->id }}">Hitung tarif ↗</button>@endif
</article>
@empty <p>Belum ada konsol.</p> @endforelse
</div></section>
@if($consoles->where('stock', '>', 0)->isNotEmpty())
<section class="panel calc"><h2>Hitung tarif</h2><label>Konsol<select id="unit">
@foreach($consoles->where('stock', '>', 0) as $item)<option value="{{ $item->id }}" data-rate="{{ $item->price }}">{{ $item->name }}</option>@endforeach
</select></label><label>Durasi<select id="hours">@for($i=1;$i<=8;$i++)<option value="{{ $i }}">{{ $i }} jam</option>@endfor</select></label><div><small>Perkiraan biaya</small><output id="total" aria-live="polite"></output></div></section>
@endif
<section class="section" id="menu"><h2>Teman mabar</h2><div class="filters" aria-label="Kategori menu">
@foreach(['Semua','Makanan','Minuman','Camilan'] as $category)<button class="quiet" type="button" data-filter="{{ $category }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}">{{ $category }}</button>@endforeach
</div><ul class="menu">
@foreach($menu as $item)<li data-category="{{ $item->category }}"><div><small>{{ $item->category }}{{ $item->stock ? '' : ' · Stok habis' }}</small><h3>{{ $item->name }}</h3></div><strong>Rp{{ number_format($item->price, 0, ',', '.') }}</strong></li>@endforeach
</ul><p id="empty-menu" @if($menu->isNotEmpty()) hidden @endif>Belum ada item di kategori ini.</p></section>
</main>
@endsection