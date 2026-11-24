(() => {
    const promotions = [...document.querySelectorAll('[data-install-promotion]')];
    const installButtons = [...document.querySelectorAll('[data-install-app]')];
    const dialog = document.querySelector('#install-guide');
    const standalone = window.matchMedia('(display-mode: standalone)');
    let deferredPrompt = null;
    const installed = () => standalone.matches || navigator.standalone === true;
    const updateVisibility = () => promotions.forEach(element => { element.hidden = installed(); });
    updateVisibility();
    standalone.addEventListener?.('change', updateVisibility);

    window.addEventListener('beforeinstallprompt', event => {
        event.preventDefault();
        deferredPrompt = event;
        installButtons.forEach(button => { button.textContent = 'Pasang aplikasi'; });
    });
    window.addEventListener('appinstalled', () => {
        deferredPrompt = null;
        promotions.forEach(element => { element.hidden = true; });
        if (dialog?.open) dialog.close();
    });

    const showGuide = () => {
        if (!dialog) return;
        const ios = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        const android = /Android/.test(navigator.userAgent);
        const steps = ios
            ? ['Buka website ini di Safari.', 'Ketuk Bagikan (ikon kotak dengan panah ke atas) pada menu browser.', 'Pilih Tambah ke Layar Utama / Add to Home Screen.', 'Jika ada pilihan Buka sebagai App, aktifkan, lalu ketuk Tambah.']
            : android
                ? ['Buka website ini di Chrome pada Android.', 'Ketuk menu tiga titik di pojok browser.', 'Pilih Instal aplikasi atau Tambahkan ke layar utama, lalu ikuti konfirmasinya.']
                : ['Buka website ini di Chrome, Edge, atau Safari yang mendukung instalasi.', 'Cari ikon instal di bilah alamat atau pilihan instal aplikasi pada menu browser.'];
        const list = dialog.querySelector('[data-install-steps]');
        list.replaceChildren(...steps.map(text => { const li = document.createElement('li'); li.textContent = text; return li; }));
        if (!dialog.open) dialog.showModal();
    };
    installButtons.forEach(button => button.addEventListener('click', async () => {
        if (!deferredPrompt) { showGuide(); return; }
        const prompt = deferredPrompt;
        deferredPrompt = null;
        button.disabled = true;
        try {
            await prompt.prompt();
            const choice = await prompt.userChoice;
            if (choice.outcome !== 'accepted') button.textContent = 'Cara memasang';
            else promotions.forEach(element => { element.hidden = true; });
        } catch {
            showGuide();
        } finally {
            button.disabled = false;
        }
    }));
    dialog?.querySelectorAll('[data-close-install]').forEach(button => button.addEventListener('click', () => dialog.close()));

    if ('serviceWorker' in navigator && window.isSecureContext) {
        window.addEventListener('load', () => {
            const registerWorker = () => navigator.serviceWorker.register('/sw.js', { scope: '/', updateViaCache: 'none' }).catch(() => {
                // Login and reservations continue to work if installation is unavailable.
            });
            if ('requestIdleCallback' in window) window.requestIdleCallback(registerWorker, { timeout: 3000 });
            else window.setTimeout(registerWorker, 1200);
        });
    }
})();
