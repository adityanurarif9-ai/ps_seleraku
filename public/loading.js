(() => {
    const root = document.documentElement;
    const bar = document.querySelector('.page-loading');
    let recovery;
    const finish = () => {
        root.classList.remove('app-booting', 'is-navigating');
        if (bar) bar.hidden = true;
        document.querySelectorAll('[data-loading="true"]').forEach(button => {
            delete button.dataset.loading;
            button.removeAttribute('aria-busy');
        });
        clearTimeout(recovery);
        try { if (window.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true) sessionStorage.setItem('ps-seleraku-booted', '1'); } catch (_) {}
    };
    const begin = (button) => {
        root.classList.add('is-navigating');
        if (bar) bar.hidden = false;
        if (button) { button.dataset.loading = 'true'; button.setAttribute('aria-busy', 'true'); }
        clearTimeout(recovery);
        recovery = window.setTimeout(finish, 15000);
    };
    window.addEventListener('pageshow', finish);
    window.addEventListener('load', finish, { once: true });
    if (document.readyState === 'complete') finish();
    else window.setTimeout(() => root.classList.remove('app-booting'), 4500);

    document.addEventListener('click', event => {
        if (event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        const link = event.target.closest?.('a[href]');
        if (!link || link.hasAttribute('download') || (link.target && link.target !== '_self')) return;
        const url = new URL(link.href, location.href);
        if (url.origin !== location.origin || !['http:', 'https:'].includes(url.protocol) || (url.pathname === location.pathname && url.search === location.search)) return;
        queueMicrotask(() => { if (!event.defaultPrevented) begin(); });
    });
    document.addEventListener('submit', event => {
        queueMicrotask(() => {
            if (event.defaultPrevented || event.target.hasAttribute('wire:submit')) return;
            begin(event.submitter || event.target.querySelector('[type="submit"]'));
        });
    });
    document.addEventListener('submit', event => {
        queueMicrotask(() => {
            if (event.defaultPrevented) return;
            const form = event.target;
            if (form.hasAttribute('wire:submit') || (form.target && form.target !== '_self')) return;
            const action = new URL(form.action || location.href, location.href);
            if (action.origin !== location.origin) return;
            begin(event.submitter || form.querySelector('[type="submit"]'));
        });
    });
})();
