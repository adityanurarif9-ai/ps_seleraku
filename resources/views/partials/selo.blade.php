<div class="selo" data-selo data-endpoint="{{ route('selo.answer') }}" data-token="{{ csrf_token() }}" data-css="{{ \App\Support\SiteAsset::url('selo.css') }}">
    <button class="selo-launch" type="button" aria-label="Tanya Selo, buka asisten PS Seleraku" aria-controls="selo-panel" aria-expanded="false">
        <span class="selo-launch-label">Tanya Selo <span aria-hidden="true">↗</span></span>
        <img src="{{ \App\Support\SiteAsset::url('selo.webp') }}" width="64" height="68" alt="" decoding="async">
    </button>
    <section id="selo-panel" class="selo-panel" role="dialog" aria-modal="false" aria-labelledby="selo-title" hidden>
        <header class="selo-header">
            <img src="{{ \App\Support\SiteAsset::url('selo.webp') }}" width="45" height="48" alt="" loading="lazy" decoding="async">
            <div><h2 id="selo-title">Halo, aku Selo!</h2><p>Asisten info PS Seleraku</p></div>
            <button type="button" class="selo-close" aria-label="Tutup chat Selo">×</button>
        </header>
        <div class="selo-log" role="log" aria-live="polite" aria-relevant="additions" aria-label="Percakapan dengan Selo">
            <p class="selo-message selo-bot">Mau main apa hari ini? Aku bantu cari tarif konsol dan cara reservasinya.</p>
        </div>
        <div class="selo-suggestions" aria-label="Pertanyaan cepat">
            <button type="button" data-question="Berapa tarif konsol?">Cek tarif</button>
            <button type="button" data-question="Bagaimana cara reservasi?">Cara reservasi</button>
            <button type="button" data-question="Bagaimana melihat sisa waktu?">Sisa waktu</button>
        </div>
        <form class="selo-form">
            <label class="selo-sr" for="selo-input">Pertanyaan untuk Selo</label>
            <input id="selo-input" name="message" placeholder="Tanya Selo…" maxlength="500" autocomplete="off" enterkeyhint="send" required>
            <button type="submit" aria-label="Kirim pertanyaan">↑</button>
        </form>
        <p class="selo-note">Info otomatis · Pesanan dikelola lewat Rental Saya.</p>
    </section>
</div>
