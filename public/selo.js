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
    const suggestions = [...widget.querySelectorAll('[data-question]')];
    let loaded = false, styleReady = false, busy = false;
    function setOpen(open) {
        if (open && !loaded) {
            const css = document.createElement('link');
            css.rel = 'stylesheet'; css.href = widget.dataset.css;
            css.addEventListener('load', () => { styleReady = true; panel.hidden = launch.getAttribute('aria-expanded') !== 'true'; });
            css.addEventListener('error', () => { loaded = false; panel.hidden = true; launch.setAttribute('aria-expanded', 'false'); });
            document.head.append(css); loaded = true;
        } else panel.hidden = !open || !styleReady;
        launch.setAttribute('aria-expanded', String(open));
        if (!open) launch.focus();
    }
    launch.addEventListener('click', () => setOpen(launch.getAttribute('aria-expanded') !== 'true'));
    close.addEventListener('click', () => setOpen(false));
    document.addEventListener('keydown', event => { if (event.key === 'Escape' && launch.getAttribute('aria-expanded') === 'true') setOpen(false); });
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
        message(text, 'user'); input.value = '';
        const pending = message('Selo sedang mencari info…', 'bot');
        const controller = new AbortController();
        const timer = setTimeout(() => controller.abort(), 15000);
        try {
            const response = await fetch(widget.dataset.endpoint, { method: 'POST', credentials: 'same-origin', signal: controller.signal,
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': widget.dataset.token},
                body: JSON.stringify({message: text}) });
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
            pending.textContent = error.message === '429' ? 'Pertanyaannya terlalu cepat. Tunggu sebentar, lalu coba lagi.' : error.message === '419' ? 'Sesi halaman sudah berakhir. Muat ulang halaman untuk melanjutkan chat.' : 'Info belum berhasil dimuat. Periksa koneksimu lalu kirim ulang pertanyaan.';
            input.value = text;
        } finally {
            clearTimeout(timer); busy = false; send.disabled = false; suggestions.forEach(b => b.disabled = false);
            log.scrollTop = log.scrollHeight;
        }
    }
    form.addEventListener('submit', event => { event.preventDefault(); if (form.reportValidity()) ask(input.value.trim()); });
    suggestions.forEach(button => button.addEventListener('click', () => ask(button.dataset.question)));
})();
