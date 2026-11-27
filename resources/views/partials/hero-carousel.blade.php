@php
    $featured = collect($classicUnits)->firstWhere('platformKey', 'ps3') ?? collect(array_merge($classicUnits, $proUnits))->first();
    $daily = collect($dailyUnits)->firstWhere('platformKey', 'ps5') ?? collect($dailyUnits)->first();
    $promoUrl = $featured ? route('reservations.create', ['catalog_item_id'=>(int)str_replace('catalog-','',$featured['id'])]) : route('page-ke2');
    $dailyUrl = $daily ? route('reservations.create', ['catalog_item_id'=>(int)str_replace('catalog-','',$daily['itemId']), 'mode'=>'daily']) : route('sewa-harian');
@endphp
<section class="ps-hero-carousel" data-hero-carousel data-interval="5000" role="region" aria-roledescription="carousel" aria-label="{{ __('Sorotan PS Seleraku') }}" data-slide-status="{{ __('Slide :current dari :total') }}">
<div class="hero-carousel-track" data-hero-track aria-live="off">
    <article class="hero-carousel-slide hero-slide-main" data-hero-slide aria-label="{{ __('PlayStation dan Nintendo') }}">
        <div class="hero-carousel-inner">
            <div class="hero-carousel-copy"><span class="hero-carousel-eyebrow">{{ __('PILIH KONSOL. AJAK TEMAN MAIN.') }}</span>
                <h1>{{ $site['hero_title'] }}</h1><p>{{ $site['hero_description'] }}</p>
                <div class="hero-carousel-actions"><a class="hero-carousel-button" href="{{ route('reservations.create') }}">{{ __('Buat reservasi') }}</a><a class="hero-carousel-link" href="{{ route('page-ke2') }}">{{ __('Lihat konsol') }}</a></div>
                <span class="hero-carousel-meta">PlayStation &amp; Nintendo · {{ __('Jadwal sesuai pilihanmu') }}</span>
            </div>
            <div class="hero-console-art" aria-hidden="true"><img src="{{ \App\Support\SiteAsset::url('images/hero-console-collection.png') }}" alt="" width="1440" height="1080" decoding="async" fetchpriority="high"></div>
        </div>
    </article>
    <article class="hero-carousel-slide hero-slide-value" data-hero-slide aria-hidden="true" inert aria-label="{{ __('Pilihan main hemat') }}">
        <div class="hero-carousel-inner">
            <div class="hero-carousel-copy"><span class="hero-promo-pill">{{ __('PILIHAN HEMAT') }}</span>
                <h2>{{ $featured['name'] ?? __('Waktunya mabar.') }}<br><span>{{ __('Seru tanpa boros.') }}</span></h2>
                <p>{{ __('Mainkan game favorit bareng teman. Pilih jadwal dan cek ketersediaan sebelum datang.') }}</p>
                @if($featured)<div class="hero-carousel-price"><strong>{{ $featured['rateText'] }}</strong><span>{{ __('/ jam') }}</span></div>@endif
                <div class="hero-carousel-actions"><a class="hero-carousel-button" href="{{ $promoUrl }}">{{ __('Pilih jadwal main') }}</a><a class="hero-carousel-link" href="{{ route('page-ke2') }}">{{ __('Lihat pilihan game') }}</a></div>
            </div>
            <div class="hero-promo-art"><span class="hero-art-orbit" aria-hidden="true"></span><div class="hero-product-frame"><img src="{{ \App\Support\SiteAsset::url($featured['image'] ?? 'images/ps3-unit.png') }}" alt="{{ $featured['name'] ?? 'PlayStation' }}" width="620" height="420" decoding="async" loading="lazy"></div><span class="hero-art-caption">{{ __('Pilih game. Ajak teman. Mulai main.') }}</span></div>
        </div>
    </article>
    <article class="hero-carousel-slide hero-slide-daily" data-hero-slide aria-hidden="true" inert aria-label="{{ __('Sewa harian di rumah') }}">
        <div class="hero-carousel-inner">
            <div class="hero-carousel-copy"><span class="hero-carousel-eyebrow">{{ __('BAWA SERUNYA KE RUMAH') }}</span>
                <h2>{{ __('Mabar di rumah.') }}<br><span>{{ __('Waktunya lebih leluasa.') }}</span></h2>
                <p>{{ __('Bawa pulang konsol favorit untuk bermain bersama teman atau keluarga. Pilih tanggal dan durasi yang pas.') }}</p>
                @if($daily)<div class="hero-carousel-price"><strong>{{ $daily['rateText'] }}</strong><span>{{ __('/ 24 jam') }}</span></div>@endif
                <div class="hero-carousel-actions"><a class="hero-carousel-button" href="{{ $dailyUrl }}">{{ __('Cek sewa harian') }}</a><a class="hero-carousel-link" href="{{ route('cara-sewa') }}">{{ __('Cara sewa') }}</a></div>
            </div>
            <div class="hero-daily-art"><div class="hero-daily-photo"><img src="{{ \App\Support\SiteAsset::url($daily['image'] ?? 'hero.png') }}" alt="{{ $daily['name'] ?? __('Sewa Harian') }}" width="620" height="420" loading="lazy" decoding="async"></div><div class="hero-daily-stamp"><strong>24</strong><span>{{ __('JAM MAIN') }}</span></div></div>
        </div>
    </article>
</div>
<button class="hero-carousel-arrow hero-carousel-prev" type="button" data-hero-prev aria-label="{{ __('Banner sebelumnya') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m15 5-7 7 7 7"/></svg></button>
<button class="hero-carousel-arrow hero-carousel-next" type="button" data-hero-next aria-label="{{ __('Banner berikutnya') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 5 7 7-7 7"/></svg></button>
<div class="hero-carousel-controls"><div class="hero-carousel-dots" role="group" aria-label="{{ __('Pilih banner') }}">@for($i=1;$i<=3;$i++)<button type="button" data-hero-dot="{{ $i-1 }}" aria-label="{{ __('Tampilkan banner :number', ['number'=>$i]) }}" aria-current="{{ $i===1 ? 'true' : 'false' }}"><span></span></button>@endfor</div>
</div>
<p class="hero-carousel-sr" data-hero-status aria-live="polite" aria-atomic="true"></p>
</section>

<script src="{{ \App\Support\SiteAsset::url('hero-carousel.js') }}" defer></script>
