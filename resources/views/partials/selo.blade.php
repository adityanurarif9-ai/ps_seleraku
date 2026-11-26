<div class="selo" data-selo data-endpoint="{{ route('selo.answer') }}" data-token="{{ csrf_token() }}" data-css="{{ \App\Support\SiteAsset::url('selo.css') }}">
    <button class="selo-launch" type="button" aria-label="{{ __('Tanya Selo, buka asisten PS Seleraku') }}" aria-controls="selo-panel" aria-expanded="false">
        <span class="selo-launch-label"><span class="selo-greeting" aria-hidden="true">{{ __('Mau main apa hari ini?') }}</span><strong>{{ __('Tanya Selo') }} <span aria-hidden="true">↗</span></strong></span>
        <span class="selo-avatar"><img src="{{ \App\Support\SiteAsset::url('selo.webp') }}" width="72" height="78" alt="" decoding="async"><span class="selo-avatar-close" aria-hidden="true">×</span></span>
    </button>
    <section id="selo-panel" class="selo-panel" role="dialog" aria-modal="false" aria-labelledby="selo-title" hidden>
        <header class="selo-header">
            <span class="selo-header-avatar"><img src="{{ \App\Support\SiteAsset::url('selo.webp') }}" width="48" height="52" alt="" loading="lazy" decoding="async"></span>
            <div><h2 id="selo-title">Selo</h2><p>{{ __('Asisten PS Seleraku · Siap bantu') }}</p></div>
            <button type="button" class="selo-close" aria-label="{{ __('Tutup chat Selo') }}">×</button>
        </header>
        <div class="selo-log" role="log" aria-live="polite" aria-relevant="additions" aria-label="{{ __('Percakapan dengan Selo') }}">
            <div class="selo-welcome">
                <img class="selo-welcome-portrait" src="{{ \App\Support\SiteAsset::url('selo.webp') }}" width="98" height="106" alt="" loading="lazy" decoding="async">
                <small>{{ __('TEMAN MABARMU') }}</small><h3>{{ __('Halo! Mau mabar hari ini?') }}</h3>
                <p>{{ __('Aku Selo. Yuk, cari konsol favoritmu atau tanya cara reservasinya.') }}</p>
            </div>
        </div>
        <div class="selo-suggestions" aria-label="{{ __('Pertanyaan cepat') }}">
            <button type="button" data-question="Berapa tarif konsol?"><span aria-hidden="true">Rp</span> {{ __('Cek tarif') }}</button>
            <button type="button" data-question="Bagaimana cara reservasi?"><span aria-hidden="true">↗</span> {{ __('Cara reservasi') }}</button>
            <button type="button" data-question="Bagaimana melihat sisa waktu?"><span aria-hidden="true">◷</span> {{ __('Sisa waktu') }}</button>
        </div>
        <form class="selo-form">
            <label class="selo-sr" for="selo-input">{{ __('Pertanyaan untuk Selo') }}</label>
            <input id="selo-input" name="message" placeholder="{{ __('Tulis pertanyaanmu…') }}" maxlength="500" autocomplete="off" enterkeyhint="send" required>
            <button type="submit" aria-label="{{ __('Kirim pertanyaan') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 4 17 8-17 8 3-8-3-8Z M7 12h14"/></svg></button>
        </form>
        <p class="selo-note">{{ __('Info otomatis · Pesanan dikelola lewat Rental Saya.') }}</p>
    </section>
</div>
