(() => {
    document.querySelectorAll('[data-hero-carousel]').forEach(root => {
        const slides = [...root.querySelectorAll('[data-hero-slide]')];
        const dots = [...root.querySelectorAll('[data-hero-dot]')];
        const track = root.querySelector('[data-hero-track]');
        const toggle = root.querySelector('[data-hero-autoplay]');
        const status = root.querySelector('[data-hero-status]');
        const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
        if (!track || slides.length < 2) return;
        let current = 0, timer = null, paused = reduced.matches, hover = false, focus = false, visible = true;
        const sync = () => {
            clearInterval(timer);
            const stopped = paused || hover || focus || !visible || document.hidden || reduced.matches;
            root.classList.toggle('is-motion-paused', stopped);
            toggle.setAttribute('aria-pressed', String(paused || reduced.matches));
            toggle.setAttribute('aria-label', paused || reduced.matches ? toggle.dataset.startLabel : toggle.dataset.stopLabel);
            if (!stopped) timer = setInterval(() => show(current + 1, false), 5000);
        };
        const show = (next, announce = true) => {
            current = (next + slides.length) % slides.length;
            root.dataset.activeSlide = String(current);
            root.dataset.theme = ['main', 'value', 'daily'][current];
            track.style.transform = `translateX(-${current * 100}%)`;
            slides.forEach((slide, index) => {
                const active = index === current;
                slide.setAttribute('aria-hidden', String(!active));
                slide.inert = !active;
                slide.querySelectorAll('a,button').forEach(el => el.setAttribute('tabindex', active ? '0' : '-1'));
                dots[index]?.setAttribute('aria-current', String(active));
            });
            if (announce && status) status.textContent = root.dataset.slideStatus.replace(':current', String(current + 1)).replace(':total', String(slides.length));
        };
        const manual = next => { show(next); sync(); };
        root.querySelector('[data-hero-prev]')?.addEventListener('click', () => manual(current - 1));
        root.querySelector('[data-hero-next]')?.addEventListener('click', () => manual(current + 1));
        dots.forEach((dot, index) => dot.addEventListener('click', () => manual(index)));
        toggle.addEventListener('click', () => { paused = !paused; sync(); });
        root.addEventListener('mouseenter', () => { hover = true; sync(); });
        root.addEventListener('mouseleave', () => { hover = false; sync(); });
        root.addEventListener('focusin', () => { focus = true; sync(); });
        root.addEventListener('focusout', event => { if (!root.contains(event.relatedTarget)) { focus = false; sync(); } });
        root.addEventListener('keydown', event => {
            if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') { event.preventDefault(); manual(current + (event.key === 'ArrowRight' ? 1 : -1)); }
        });
        let start = null, swiped = false;
        root.addEventListener('touchstart', event => { const t = event.changedTouches[0]; start = [t.clientX, t.clientY]; swiped = false; }, { passive:true });
        root.addEventListener('touchend', event => { if (!start) return; const t = event.changedTouches[0], dx = t.clientX - start[0], dy = t.clientY - start[1]; if (Math.abs(dx) > 55 && Math.abs(dy) < 60) { swiped = true; manual(current + (dx < 0 ? 1 : -1)); } start = null; }, { passive:true });
        root.addEventListener('click', event => { if (swiped) { event.preventDefault(); event.stopPropagation(); swiped = false; } }, true);
        document.addEventListener('visibilitychange', sync);
        reduced.addEventListener?.('change', () => { paused = reduced.matches; sync(); });
        if ('IntersectionObserver' in window) new IntersectionObserver(entries => { visible = entries[0].isIntersecting; sync(); }).observe(root);
        window.addEventListener('pagehide', () => clearInterval(timer));
        window.addEventListener('pageshow', sync);
        show(0, false); sync();
    });
})();
