<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Menu Camilan & Minuman Barista — PS SELERAKU') }}</title>
@include('partials.performance-head', ['authPage'=>false])


    <link rel="stylesheet" href="{{ \App\Support\SiteAsset::url('seleraku-pages.css?v=logo-sync-1') }}">
    <script src="{{ \App\Support\SiteAsset::url('seleraku-pages.js') }}?v=catalog-sync-2" defer></script>
@include('partials.pwa-head')
</head>
<body class="ps-page-body" data-reservation-url="{{ route('reservations.create') }}">
@include('partials.pwa-loading')
    @include('partials.ps-navbar', ['activePage' => 'menu-fnb'])

    <main>
        <!-- HERO BANNER -->
        <section class="ps-hero-page2 ps-hero-food" aria-label="Hero Lounge F&B PS Seleraku">
            <div class="ps-hero-inner">
                <h1 class="ps-hero-title">
                    {{ __('TEMAN') }} <span class="ps-text-ps4">{{ __('NGEMIL') }}</span> &amp; <span class="ps-text-ps5">{{ __('MABAR') }}</span>
                </h1>
                <p class="ps-hero-subtitle">
                    {{ __('Temukan makanan, camilan, dan minuman favorit untuk menemani sesi mainmu.') }}
                </p>
            </div>
        </section>

        <!-- LOUNGE F&B CATALOG (DESKTOP - 4) -->
        <section id="fnb-catalog" class="ps-fnb-section" aria-label="{{ __('Menu Camilan dan Minuman Barista') }}">
            <div class="ps-container">
                <div class="ps-fnb-header">
                    <div class="ps-fnb-header-left">
                        <div class="ps-fnb-eyebrow-row">
                            <span class="ps-fnb-pill-badge">LOUNGE REFRESHMENT</span>
                            <span class="ps-fnb-eyebrow">CONCIERGE F&amp;B DELIVERY</span>
                        </div>
                        <h2 class="ps-fnb-title">{{ __('Menu Camilan & Minuman Barista') }}</h2>
                        <p class="ps-fnb-subtitle">
                            {{ __('Pesan langsung dari sofa VIP konsol Anda tanpa menjeda aksi seru mabar. Diantar hangat langsung ke meja.') }}
                        </p>
                    </div>

                    <div class="ps-fnb-search-wrap">
                        <svg class="ps-fnb-search-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                        <input
                            id="fnb-search"
                            type="search"
                            placeholder="{{ __('Cari makanan, snack gurih, kopi, atau es...') }}"
                            aria-label="{{ __('Cari makanan, snack gurih, kopi, atau minuman') }}"
                        >
                        <button id="fnb-search-clear" type="button" class="ps-fnb-clear-btn" aria-label="{{ __('Hapus pencarian menu') }}" hidden>&times;</button>
                    </div>
                </div>

                <!-- CATEGORY FILTER PILLS -->
                <div class="ps-fnb-filters" role="group" aria-label="{{ __('Kategori Menu Lounge') }}">
                    <button type="button" class="ps-fnb-cat-btn is-active" data-fnb-filter="all">{{ __('Semua Menu') }}</button>
                    <button type="button" class="ps-fnb-cat-btn" data-fnb-filter="makanan-berat">{{ __('Makanan Berat') }}</button>
                    <button type="button" class="ps-fnb-cat-btn" data-fnb-filter="camilan">{{ __('Camilan & Gorengan') }}</button>
                    <button type="button" class="ps-fnb-cat-btn" data-fnb-filter="minuman">{{ __('Minuman Dingin & Kopi') }}</button>
                    <button type="button" class="ps-fnb-cat-btn is-combo" data-fnb-filter="combo">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#f43f5e" stroke-width="2.2" aria-hidden="true">
                            <path d="M12 2L14.8 8.5L22 9.3L16.5 14L18.2 21L12 17.3L5.8 21L7.5 14L2 9.3L9.2 8.5L12 2Z"/>
                        </svg>
                        <span>{{ __('Paket Combo Mabar') }}</span>
                    </button>
                </div>

                <!-- SUB-BAR COUNT & KITCHEN INFO -->
                <div class="ps-fnb-subbar">
                    <div class="ps-fnb-subbar-left">
                        <h3>{{ __('Katalog Pilihan Meja VIP') }}</h3>
                        <span id="fnb-count" class="ps-fnb-count-badge">{{ count($fnbItems) }} {{ __('pilihan menu') }}</span>
                    </div>
                    <div class="ps-fnb-subbar-right">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.2" aria-hidden="true">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>
                        <span>{{ __('Dapur Lounge Higienis & Freshly Cooked') }}</span>
                    </div>
                </div>


                <!-- 3-COLUMN MENU GRID -->
                <div class="ps-fnb-grid">
                    @foreach ($fnbItems as $item)
                        <article
                            class="ps-fnb-card"
                            data-fnb-card
                            data-fnb-id="{{ $item['id'] }}"
                            data-fnb-name="{{ $item['name'] }}"
                            data-fnb-price="{{ $item['price'] }}"
                            data-fnb-categories="{{ $item['categories'] }}"
                            data-fnb-search="{{ $item['search'] }}"
                        >
                            <div class="ps-fnb-thumb">
                                <img src="{{ \App\Support\SiteAsset::url($item['image']) }}" alt="{{ $item['name'] }}" loading="lazy" decoding="async">
                                <span class="ps-fnb-tag {{ $item['badgeClass'] }}">{{ $item['badge'] }}</span>
                                @if ($item['rating'])
                                    <span class="ps-fnb-rating">
                                        <span class="ps-fnb-star" aria-hidden="true">&#9733;</span>
                                        {{ $item['rating'] }}
                                    </span>
                                @endif
                            </div>

                            <div class="ps-fnb-body">
                                <h3 class="ps-fnb-card-title">{{ $item['name'] }}</h3>
                                <p class="ps-fnb-card-desc">{{ $item['desc'] }}</p><p class="ps-fnb-card-desc">{{ __('Stok:') }} {{ $item['stock'] }}</p>

                                <div class="ps-fnb-card-foot">
                                    <div>
                                        <span class="ps-fnb-price-label">{{ __('HARGA') }}</span>
                                        <strong class="ps-fnb-price-val">{{ $item['priceText'] }}</strong>
                                    </div>

                                    @include('partials.cart-add', ['cartItemId'=>(int) str_replace('menu-','',$item['id']), 'cartDisabled'=>$item['stock']<1])
                                </div>
                            </div>
                        </article>
                    @endforeach

                    <div id="fnb-empty" class="ps-empty-state" @if(count($fnbItems)>0) hidden @endif>
                        <h3>{{ __('Menu Tidak Ditemukan') }}</h3>
                        <p>{{ __('Tidak ada camilan atau minuman yang cocok dengan filter atau kata kunci Anda.') }}</p>
                        <button type="button" id="fnb-reset-filters" class="ps-btn-primary">{{ __('Tampilkan Semua Menu') }}</button>
                    </div>
                </div>

                <!-- HYGIENE & WAITER CALLOUT CARD -->
                <div class="ps-fnb-hygiene-card">
                    <div class="ps-fnb-hygiene-left">
                        <div class="ps-fnb-hygiene-ico" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                                <path d="M9 12l2 2 4-4"/>
                                <path d="M12 3a9 9 0 1 0 9 9"/>
                                <path d="M18 3v4"/>
                                <path d="M16 5h4"/>
                            </svg>
                        </div>
                        <div>
                            <h4>{{ __('Peralatan Higienis & Controller Friendly') }}</h4>
                            <p>{{ __('Setiap pesanan camilan dilengkapi tissue basah pembersih stik controller antibakteri & sendok garpu higienis.') }}</p>
                        </div>
                    </div>

                    <div class="ps-fnb-hygiene-right">
                        <span>{{ __('Butuh bantuan waiter?') }}</span>
                        <a href="{{ route('kontak') }}" class="ps-fnb-crew-btn">{{ __('Hubungi pengelola') }}</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('partials.ps-footer')
    @include('partials.ps-modals')
</body>
</html>
