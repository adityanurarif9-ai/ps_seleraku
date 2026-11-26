<aside class="install-app" data-install-promotion aria-label="{{ __('Pasang PS Seleraku') }}">
    <img src="{{ \App\Support\SiteAsset::url('icons/brand-v2-192.png') }}" alt="" width="44" height="44" decoding="async">
    <div><strong>{{ __('PS Seleraku di ponselmu') }}</strong><p>{{ __('Akses cepat dari layar utama.') }}</p></div>
    <button type="button" data-install-app>{{ __('Pasang') }}</button>
</aside>
<dialog class="install-guide" id="install-guide" aria-labelledby="install-guide-title">
    <div class="install-guide-top"><img src="{{ \App\Support\SiteAsset::url('icons/brand-v2-192.png') }}" alt="" width="52" height="52" decoding="async"><button type="button" data-close-install aria-label="{{ __('Tutup panduan pemasangan') }}">×</button></div>
    <h2 id="install-guide-title">{{ __('Pasang PS Seleraku') }}</h2>
    <p data-install-intro>{{ __('Buka langsung dari layar utama, tanpa mengetik alamat website lagi.') }}</p>
    <ol data-install-steps></ol>
    <p class="install-guide-note">{{ __('Gunakan koneksi internet untuk masuk, memesan konsol, dan melihat sisa waktu terbaru.') }}</p>
    <button type="button" class="install-guide-done" data-close-install>{{ __('Mengerti') }}</button>
</dialog>
