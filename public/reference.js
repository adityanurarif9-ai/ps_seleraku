(() => {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  document.querySelectorAll('.ps-hero-inner,.web-home-hero .web-hero-grid').forEach(el => el.classList.add('ref-enter'));
  if (!('IntersectionObserver' in window)) return;
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('ref-reveal');
      observer.unobserve(entry.target);
    });
  }, { threshold: .08 });
  document.querySelectorAll('.ps-unit-card-light,.ps-unit-card-pro,.ps-daily-card,.ps-fnb-card,.web-console-card,.web-package,.web-info-card,.web-footer-invite').forEach(el => observer.observe(el));
})();
(() => {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    document.querySelectorAll('.ps-controller-scene').forEach(scene => {
        const button = scene.parentElement.querySelector('[data-motion-toggle]');
        let manualPause = false, visible = true;
        const sync = () => scene.classList.toggle('is-paused', manualPause || !visible || document.hidden || reduced.matches);
        button?.addEventListener('click', () => {
            manualPause = !manualPause;
            button.setAttribute('aria-pressed', String(manualPause));
            button.textContent = manualPause ? button.dataset.playLabel : button.dataset.pauseLabel;
            sync();
        });
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(entries => { visible = entries[0].isIntersecting; sync(); });
            observer.observe(scene);
        }
        document.addEventListener('visibilitychange', sync);
        reduced.addEventListener?.('change', sync);
        sync();
    });
})();
