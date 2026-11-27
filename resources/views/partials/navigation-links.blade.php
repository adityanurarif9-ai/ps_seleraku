@php
    $navigationGroups = [
        ['label'=>'SEWA', 'pages'=>['page-ke2','sewa-harian','rentals'], 'links'=>[
            ['page-ke2','page-ke2','Konsol & Game'],['sewa-harian','sewa-harian','Sewa Harian'],['rentals','reservations.index','Rental Saya'],
        ]],
        ['label'=>'KEDAI', 'pages'=>['menu-fnb','food-orders'], 'links'=>[
            ['menu-fnb','menu-fnb','Makanan & Minuman'],['food-orders','food-orders.index','Pesanan Makanan'],
        ]],
        ['label'=>'INFORMASI', 'pages'=>['tentang','cara-sewa','kontak'], 'links'=>[
            ['tentang','tentang','Tentang kami'],['cara-sewa','cara-sewa','Cara sewa'],['kontak','kontak','Kontak'],
        ]],
    ];
@endphp
<a class="ps-nav-link {{ $activePage === 'home-page' ? 'is-active' : '' }}" href="{{ route('home-page') }}" @if($activePage === 'home-page') aria-current="page" @endif><span>{{ __('BERANDA') }}</span></a>
@foreach($navigationGroups as $group)
<details class="ps-nav-branch" data-nav-branch>
    <summary class="ps-nav-link {{ in_array($activePage,$group['pages']) ? 'is-active' : '' }}"><span>{{ __($group['label']) }}</span><svg viewBox="0 0 20 20" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m5 7 5 5 5-5"/></svg></summary>
    <div class="ps-nav-submenu">
        @foreach($group['links'] as [$page,$route,$label])
        <a href="{{ route($route) }}" @if($activePage === $page) aria-current="page" @endif>{{ __($label) }}</a>
        @endforeach
    </div>
</details>
@endforeach
