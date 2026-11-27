<section class="ps-console-intro" aria-labelledby="console-intro-title">
    <div class="web-wrap ps-console-intro-inner">
        <div class="ps-console-intro-copy">
            <span class="web-eyebrow">PS SELERAKU / PLAYSTATION &amp; NINTENDO</span>
            <h2 id="console-intro-title"><span>{{ __('Pilih PlayStation') }}</span><br>{{ __('favoritmu.') }}</h2>
            <p>{{ __('Dari PS3, PS4, PS5 sampai Nintendo Switch. Lihat koleksi game, pilih durasi, lalu pesan jadwal main bersama teman.') }}</p>
            <a class="ps-console-intro-link" href="{{ route('page-ke2') }}">{{ __('Temukan konsolmu') }} <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 12h15m-6-6 6 6-6 6"/></svg></a>
        </div>
        <div class="ps-console-intro-art" aria-hidden="true">
            <span class="ps-console-intro-ring"></span>
            <img class="ps-console-intro-ps5" src="{{ \App\Support\SiteAsset::url('images/hero-ps5-cutout.png') }}" alt="" width="1024" height="1024" loading="lazy" decoding="async">
            <span class="ps-console-intro-controller" style="background-image:url('{{ \App\Support\SiteAsset::url('images/hero-controllers-clean.png') }}')"></span>
            <span class="ps-console-intro-caption">{{ __('Pilih konsol. Siapkan mabar.') }}</span>
        </div>
    </div>
</section>
