@extends('site.layout', ['activePage'=>'food-orders'])
@section('title', __('Pesanan Makanan'))
@section('content')
<section class="web-wrap web-section cart-page">
<div class="web-section-top"><div><span class="web-eyebrow">{{ __('KEDAI PS SELERAKU') }}</span><h1>{{ __('Pesanan Makanan') }}</h1><p>{{ __('Pesananmu tersimpan di sini. Muat ulang untuk melihat status terbaru dari pengelola.') }}</p></div><a class="web-button" href="{{ route('menu-fnb') }}">{{ __('Pilih makanan & minuman') }}</a></div>
@if(session('order_notice'))<p class="cart-notice" role="status">{{ session('order_notice') }}</p>@endif
@if($errors->any())<p class="cart-error" role="alert">{{ $errors->first() }}</p>@endif
<div class="cart-lines">
@forelse($orders as $order)
<article class="cart-line"><div class="cart-line-body"><span class="cart-type">{{ $order->reference() }} · {{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span><h2>{{ $order->statusLabel() }}</h2>
@foreach($order->items as $line)<p>{{ $line->quantity }} × {{ $line->name }} <strong>Rp{{ number_format($line->unit_price*$line->quantity,0,',','.') }}</strong></p>@endforeach
<p class="cart-price">{{ __('Total') }}: Rp{{ number_format($order->total_price,0,',','.') }}</p>
@if($order->notes)<p>{{ __('Catatan') }}: {{ $order->notes }}</p>@endif
<p class="cart-footnote">{{ __('Pembayaran digital tersedia dalam mode uji. Hasil simulasi bukan bukti lunas.') }}</p>
@if(in_array($order->status,['pending','preparing','ready']))<form method="post" action="{{ route('payments.food',$order) }}">@csrf<button class="cart-reserve" type="submit">{{ __('Pembayaran digital (uji)') }}</button></form>@endif
@if($order->status==='pending')<form action="{{ route('food-orders.cancel',$order) }}" method="post">@csrf<button class="cart-remove" type="submit">{{ __('Batalkan pesanan') }}</button></form>@endif
</div></article>
@empty<div class="cart-empty"><h2>{{ __('Belum ada pesanan makanan') }}</h2><p>{{ __('Pilih menu, tambahkan ke keranjang, lalu kirim pesanan.') }}</p></div>@endforelse
</div><div style="margin-top:24px">{{ $orders->links() }}</div>
</section>
@endsection
