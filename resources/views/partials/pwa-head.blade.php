<link rel="manifest" href="{{ \App\Support\SiteAsset::url('manifest.webmanifest') }}?v=brand-2">
<link rel="apple-touch-icon" sizes="180x180" href="{{ \App\Support\SiteAsset::url('icons/brand-v2-apple.png') }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="PS Seleraku">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="theme-color" content="#0a0d25">
@unless($authPage ?? false)
<link rel="stylesheet" href="{{ \App\Support\SiteAsset::url('pwa.css') }}?v=brand-2">
@endunless
<script src="{{ \App\Support\SiteAsset::url('pwa.js') }}?v=brand-2" defer></script>
<script>
(() => {
    try {
        const installed = window.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;
        if (installed && !sessionStorage.getItem('ps-seleraku-booted')) {
            document.documentElement.classList.add('app-booting');
            window.setTimeout(() => document.documentElement.classList.remove('app-booting'), 4500);
        }
    } catch (_) {}
})();
</script>
<script src="{{ \App\Support\SiteAsset::url('loading.js') }}?v=1" defer></script>
