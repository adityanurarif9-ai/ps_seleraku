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
    const branches = [...document.querySelectorAll('[data-nav-branch]')];
    branches.forEach(branch => {
        branch.addEventListener('toggle', () => { if (branch.open) branches.forEach(other => { if (other !== branch) other.open = false; }); });
        branch.addEventListener('keydown', event => { if (event.key === 'Escape' && branch.open) { event.preventDefault(); branch.open = false; branch.querySelector('summary').focus(); } });
        branch.addEventListener('focusout', event => { if (event.relatedTarget && !branch.contains(event.relatedTarget)) branch.open = false; });
    });
    document.addEventListener('click', event => { branches.forEach(branch => { if (!branch.contains(event.target)) branch.open = false; }); });
})();
