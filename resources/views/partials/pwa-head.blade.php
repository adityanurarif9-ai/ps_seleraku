@if(auth()->check() || request()->routeIs('tentang', 'cara-sewa', 'kontak'))
<link rel="stylesheet" href="{{ \App\Support\SiteAsset::url('site.css') }}">
<link rel="stylesheet" href="{{ \App\Support\SiteAsset::url('reference.css') }}">
<script src="{{ \App\Support\SiteAsset::url('reference.js') }}" defer></script>
@endif
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

<style>.selo{position:fixed;right:max(20px,env(safe-area-inset-right,0px));bottom:max(18px,env(safe-area-inset-bottom,0px));z-index:800;font-family:Poppins,Arial,sans-serif}.selo-launch{position:relative;display:flex;align-items:center;gap:13px;padding:0;border:0;background:transparent;color:#173c71;cursor:pointer}.selo-launch-label{position:absolute;right:0;bottom:calc(100% + 14px);width:208px;display:block;text-align:left;background:#fff;padding:12px 17px;border:1px solid #d7e2f0;border-radius:17px;box-shadow:0 5px 24px #122c5918;animation:selo-nudge 3s ease-in-out infinite;transition:opacity .18s,transform .18s}.selo-greeting{display:block;font:400 10px/1.5 Poppins,Arial,sans-serif;color:#60738d;margin-bottom:3px}.selo-launch-label strong{display:block;font:600 12px/1.5 Poppins,Arial,sans-serif}.selo-launch-label strong span{margin-left:12px}.selo-avatar{position:relative;display:grid;place-items:center;width:76px;height:82px;border:1px solid #cbd9eb;border-radius:27px;background:#fff;box-shadow:0 8px 26px #132e5726;transition:transform .2s,border-radius .2s;animation:selo-arrive .65s ease-out}.selo-avatar::after{content:"";position:absolute;right:0;bottom:4px;width:10px;height:10px;background:#75bd93;border:3px solid #fff;border-radius:50%}.selo-avatar img{display:block;width:72px;height:78px;object-fit:contain;border-radius:26px;transition:opacity .15s}.selo-avatar-close{display:none;font:300 30px/1 Arial;color:#fff}.selo-is-open .selo-launch-label{display:none}.selo-is-open .selo-avatar{background:#244d86;border-color:#244d86;border-radius:50%;width:58px;height:58px}.selo-is-open .selo-avatar img,.selo-is-open .selo-avatar::after{display:none}.selo-is-open .selo-avatar-close{display:block}.selo-launch:focus-visible{outline:3px solid #6891cf;outline-offset:4px}.selo-panel[hidden]{display:none!important}.selo-launch-label::after{content:"";position:absolute;right:28px;bottom:-6px;width:10px;height:10px;background:#fff;border-right:1px solid #d7e2f0;border-bottom:1px solid #d7e2f0;transform:rotate(45deg)}.selo-away .selo-launch-label{animation-play-state:paused}@keyframes selo-nudge{0%,10%,100%{transform:translateY(8px) scale(.97);visibility:hidden}20%,78%{transform:translateY(0) scale(1);visibility:visible}90%{transform:translateY(8px) scale(.97);visibility:hidden}}@keyframes selo-arrive{0%{opacity:0;transform:translateY(12px) rotate(8deg)}65%{opacity:1;transform:translateY(-3px) rotate(-3deg)}100%{transform:translateY(0) rotate(0)}}@media(hover:hover){.selo-launch:hover .selo-avatar{transform:translateY(-5px) rotate(-4deg)}.selo-launch:hover .selo-launch-label{border-color:#9eb5d5}}@media(max-width:480px){.selo{right:14px;bottom:max(14px,env(safe-area-inset-bottom,0px))}.selo-avatar{width:64px;height:70px;border-radius:23px}.selo-avatar img{width:60px;height:66px;border-radius:22px}.selo-launch{gap:9px}.selo-launch-label{padding:10px 13px}.selo-greeting{font-size:9px}.selo-launch-label strong{font-size:11px}}@media(prefers-reduced-motion:reduce){.selo-launch,.selo-launch *{animation:none!important;transition:none!important}}</style>
<script src="{{ \App\Support\SiteAsset::url('selo.js') }}" defer></script>
