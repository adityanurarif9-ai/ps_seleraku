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

<style>.selo{position:fixed;right:max(20px,env(safe-area-inset-right,0px));bottom:max(18px,env(safe-area-inset-bottom,0px));z-index:800;font-family:Poppins,Arial,sans-serif}.selo-launch{display:flex;align-items:center;gap:9px;padding:0;border:0;background:transparent;color:#fff;cursor:pointer}.selo-launch img{display:block;width:64px;height:68px;object-fit:contain;background:#fff;border:1px solid #d8e4f5;border-radius:18px;box-shadow:0 5px 20px #122c5930}.selo-launch-label{background:#284c85;padding:11px 15px;border:1px solid #86a3ce;border-radius:14px;font:600 12px/1.5 Poppins,Arial,sans-serif;box-shadow:0 4px 15px #122c5920}.selo-launch-label span{margin-left:8px}.selo-launch:focus-visible{outline:3px solid #6891cf;outline-offset:4px}.selo-panel[hidden]{display:none!important}@media(max-width:480px){.selo{right:14px;bottom:max(14px,env(safe-area-inset-bottom,0px))}.selo-launch img{width:56px;height:60px}.selo-launch-label{font-size:11px;padding:10px 12px}}</style>
<script src="{{ \App\Support\SiteAsset::url('selo.js') }}" defer></script>
