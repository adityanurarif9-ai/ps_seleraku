@extends('site.layout', ['activePage'=>'cart'])
@section('title', __('Keranjang'))
@section('content')
<section class="web-wrap web-section cart-page">
<div class="web-section-top"><div><span class="web-eyebrow">{{ __('PILIHANMU') }}</span><h1>{{ __('Keranjang') }}</h1><p>{{ __('Pilih jadwal untuk reservasi konsol. Konfirmasi pesanan menu melalui pengelola.') }}</p></div><a class="web-text-link" href="{{ route('page-ke2') }}">{{ __('Lanjut pilih konsol') }}</a></div>
@if(session('cart_notice'))<p class="cart-notice" role="status">{{ session('cart_notice') }}</p>@endif
@if($errors->any())<div class="cart-error" role="alert">{{ $errors->first() }}</div>@endif
@if($lines->isEmpty())
<div class="cart-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="M3 3h2l3 12h10l3-9H6M10 20h.01M18 20h.01"/></svg><h2>{{ __('Keranjangmu masih kosong') }}</h2><p>{{ __('Simpan konsol atau menu favoritmu untuk melanjutkan nanti.') }}</p><div class="web-actions"><a class="web-button" href="{{ route('page-ke2') }}">{{ __('Lihat konsol') }}</a><a class="web-text-link" href="{{ route('menu-fnb') }}">{{ __('Menu & Kedai') }}</a></div></div>
@else
<div class="cart-layout"><div class="cart-lines">
@foreach($lines as $line)
@php($item=$line['item'])
<article class="cart-line"><img src="{{ \App\Support\SiteAsset::url($item->image()) }}" alt="{{ $item->name }}" width="120" height="100" loading="lazy" decoding="async"><div class="cart-line-body"><span class="cart-type">{{ $item->category==='Konsol' ? ($line['mode']==='daily' ? __('Sewa Harian') : __('Main di Lounge')) : __($item->category) }}</span><h2>{{ $item->name }}</h2><p class="cart-price">{{ $line['price']===null ? __('Tidak tersedia') : 'Rp'.number_format($line['price'],0,',','.') }} @if($item->category==='Konsol')<small>{{ $line['mode']==='daily' ? __('/ 24 jam') : __('/ jam') }}</small>@endif</p>
@if(!$line['available'])<p class="cart-warning">{{ __('Stok atau tarif berubah. Periksa pilihan ini sebelum melanjutkan.') }}</p>@endif
<div class="cart-line-actions">
@if($item->category==='Konsol')
@if($line['available'])<a class="cart-reserve" href="{{ route('reservations.create',['catalog_item_id'=>$item->id,'mode'=>$line['mode']]) }}">{{ __('Pilih jadwal') }}</a>@endif
@else
<form action="{{ route('cart.update',$item) }}" method="post" class="cart-quantity">@csrf @method('PATCH')<label for="cart-qty-{{ $item->id }}">{{ __('Jumlah') }}</label><input id="cart-qty-{{ $item->id }}" name="quantity" type="number" min="1" max="{{ min(99,max(1,$item->stock)) }}" value="{{ $line['quantity'] }}" required><button type="submit">{{ __('Simpan') }}</button></form>
@endif
<form action="{{ route('cart.destroy',$item) }}" method="post">@csrf @method('DELETE')<button class="cart-remove" type="submit">{{ __('Hapus') }}</button></form>
</div></div></article>
@endforeach
</div><aside class="cart-summary"><h2>{{ __('Rencana mainmu') }}</h2><p>{{ __('Biaya rental dihitung setelah kamu memilih jadwal dan durasi.') }}</p><dl><div><dt>{{ __('Pilihan konsol') }}</dt><dd>{{ $lines->where('item.category','Konsol')->count() }}</dd></div><div><dt>{{ __('Jumlah menu') }}</dt><dd>{{ $lines->filter(fn($l)=>$l['item']->category!=='Konsol')->sum('quantity') }}</dd></div><div><dt>{{ __('Subtotal menu') }}</dt><dd>Rp{{ number_format($foodTotal,0,',','.') }}</dd></div></dl><a class="web-button" href="{{ route('kontak') }}">{{ __('Hubungi pengelola') }}</a><a class="web-text-link" href="{{ route('menu-fnb') }}">{{ __('Tambah menu') }}</a><p class="cart-footnote">{{ __('Isi keranjang belum menjadi pesanan atau pembayaran. Jadwal konsol tetap memerlukan konfirmasi pengelola.') }}</p></aside></div>
@endif
</section>
@endsection
