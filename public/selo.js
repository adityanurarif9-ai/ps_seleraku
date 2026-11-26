var psTr = window.psT || (value => value);
(() => {
    const widget = document.querySelector('[data-selo]');
    if (!widget) return;
    const launch = widget.querySelector('.selo-launch');
    const panel = widget.querySelector('.selo-panel');
    const close = widget.querySelector('.selo-close');
    const form = widget.querySelector('form');
    const input = form.querySelector('input');
    const send = form.querySelector('button');
    const log = widget.querySelector('.selo-log');
    const welcome = widget.querySelector('.selo-welcome');
    const suggestions = [...widget.querySelectorAll('[data-question]')];
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let loaded = false, styleReady = false, busy = false, animation;
    const isOpen = () => launch.getAttribute('aria-expanded') === 'true';
    function fitViewport() {
        const viewport = window.visualViewport;
        if (!viewport || !isOpen()) return;
        widget.style.setProperty('--selo-viewport', viewport.height + 'px');
        widget.style.setProperty('--selo-keyboard', Math.max(0, window.innerHeight - viewport.height - viewport.offsetTop) + 'px');
    }
    function reveal() {
        animation?.cancel();
        panel.hidden = false;
        fitViewport();
        if (!reducedMotion.matches && panel.animate) {
            animation = panel.animate([{opacity:0,transform:'translateY(16px) scale(.96)'},{opacity:1,transform:'translateY(0) scale(1)'}], {duration:240,easing:'cubic-bezier(.2,.7,.2,1)'});
        }
    }
    function setOpen(open) {
        launch.setAttribute('aria-expanded', String(open));
        widget.classList.toggle('selo-is-open', open);
        animation?.cancel();
        if (!open) {
            if (!panel.hidden && !reducedMotion.matches && panel.animate) {
                animation = panel.animate([{opacity:1,transform:'translateY(0) scale(1)'},{opacity:0,transform:'translateY(10px) scale(.97)'}], {duration:150,easing:'ease-in'});
                animation.onfinish = () => { if (!isOpen()) panel.hidden = true; };
            } else panel.hidden = true;
            launch.focus();
            return;
        }
        if (styleReady) { reveal(); return; }
        if (loaded) return;
        const css = document.createElement('link');
        css.rel = 'stylesheet'; css.href = widget.dataset.css;
        css.addEventListener('load', () => { styleReady = true; if (isOpen()) reveal(); });
        css.addEventListener('error', () => {
            loaded = false; css.remove(); panel.hidden = true;
            launch.setAttribute('aria-expanded', 'false');
            widget.classList.remove('selo-is-open');
        });
        document.head.append(css); loaded = true;
    }
    launch.addEventListener('click', () => setOpen(!isOpen()));
    close.addEventListener('click', () => setOpen(false));
    document.addEventListener('keydown', event => { if (event.key === 'Escape' && isOpen()) setOpen(false); });
    window.visualViewport?.addEventListener('resize', fitViewport);
    window.visualViewport?.addEventListener('scroll', fitViewport);
    document.addEventListener('visibilitychange', () => {
        widget.classList.toggle('selo-away', document.hidden);
    });
    function message(text, who) {
        const p = document.createElement('p');
        p.className = 'selo-message selo-' + who; p.textContent = text;
        log.append(p);
        while (log.children.length > 40) log.firstElementChild.remove();
        log.scrollTop = log.scrollHeight;
        return p;
    }
    async function ask(text) {
        if (busy || !text.trim()) return;
        busy = true; send.disabled = true; suggestions.forEach(b => b.disabled = true);
        if (welcome) welcome.hidden = true;
        message(text, 'user'); input.value = '';
        const pending = message('', 'bot');
        pending.classList.add('selo-typing');
        const label = document.createElement('span');
        label.className = 'selo-sr'; label.textContent = psTr('Selo sedang mencari info…');
        pending.append(label);
        for (let i = 0; i < 3; i++) {
            const dot = document.createElement('i'); dot.setAttribute('aria-hidden', 'true'); pending.append(dot);
        }
        log.setAttribute('aria-busy', 'true');
        const controller = new AbortController();
        const timer = setTimeout(() => controller.abort(), 15000);
        try {
            const response = await fetch(widget.dataset.endpoint, {method:'POST',credentials:'same-origin',signal:controller.signal,
                headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':widget.dataset.token},
                body:JSON.stringify({message:text})});
            if (!response.ok) throw new Error(String(response.status));
            const data = await response.json();
            if (typeof data.reply !== 'string') throw new Error('invalid');
            pending.textContent = data.reply;
            for (const item of (Array.isArray(data.links) ? data.links : []).slice(0, 3)) {
                const url = new URL(item.url, location.origin);
                if (url.origin !== location.origin || !['http:', 'https:'].includes(url.protocol)) continue;
                const a = document.createElement('a'); a.href = url.href; a.textContent = item.label; a.className = 'selo-link'; pending.append(a);
            }
        } catch (error) {
            pending.textContent = error.message === '429' ? psTr('Pertanyaannya terlalu cepat. Tunggu sebentar, lalu coba lagi.') : error.message === '419' ? psTr('Sesi halaman sudah berakhir. Muat ulang halaman untuk melanjutkan chat.') : psTr('Info belum berhasil dimuat. Periksa koneksimu lalu kirim ulang pertanyaan.');
            input.value = text;
        } finally {
            clearTimeout(timer); pending.classList.remove('selo-typing');
            log.setAttribute('aria-busy', 'false');
            busy = false; send.disabled = false; suggestions.forEach(b => b.disabled = false);
            log.scrollTop = log.scrollHeight;
        }
    }
    form.addEventListener('submit', event => {event.preventDefault(); if (form.reportValidity()) ask(input.value.trim());});
    suggestions.forEach(button => button.addEventListener('click', () => ask(button.dataset.question)));
})();
