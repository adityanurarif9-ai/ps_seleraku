@auth
@php
$bottomItems=[
 ['home-page','Beranda',request()->routeIs('home-page'),'M3 10l9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1Z'],
 ['page-ke2','Konsol',request()->routeIs('page-ke2','sewa-harian','reservations.create'),'M6 8h12l3 9a2 2 0 0 1-3 2l-3-3H9l-3 3a2 2 0 0 1-3-2Zm1 3v4m-2-2h4m7-1h.01M18 14h.01'],
 ['menu-fnb','Kedai',request()->routeIs('menu-fnb','food-orders.*'),'M4 3v6a3 3 0 0 0 6 0V3M7 3v18M18 3c-4 3-4 9 0 9h2V3h-2Zm2 9v9'],
 ['reservations.index','Rental Saya',request()->routeIs('reservations.index','reservations.show','payments.*'),'M8 3v4m8-4v4M4 10h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Zm3 9h3m-3 3h6'],
 ['cart.index','Keranjang',request()->routeIs('cart.*'),'M3 3h2l3 12h10l3-9H6M10 20h.01M18 20h.01']
];
@endphp
<nav class="ps-bottom-nav" aria-label="{{ __('Navigasi bawah') }}">
@foreach($bottomItems as [$route,$label,$isCurrent,$path])
<a href="{{ route($route) }}" class="ps-bottom-item {{ $isCurrent?'is-current':'' }}" @if($isCurrent) aria-current="page" @endif><span class="ps-bottom-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $path }}"/></svg></span><span>{{ __($label) }}</span></a>
@endforeach
</nav>
@endauth
