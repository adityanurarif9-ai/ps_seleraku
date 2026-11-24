(() => {
    const money = v => 'Rp' + new Intl.NumberFormat('id-ID').format(v);
    const form = document.querySelector('#rs-booking-form');
    if (form) {
        const mode = form.querySelector('[name="mode"]');
        const duration = form.querySelector('[name="duration"]');
        const button = document.querySelector('#rs-submit');
        const message = document.querySelector('#rs-availability');
        let controller;
        const check = async () => {
            controller?.abort(); controller = new AbortController();
            button.disabled = true;
            const params = new URLSearchParams();
            ['catalog_item_id','mode','duration','starts_at'].forEach(name => params.set(name, form.elements[name].value));
            if (!params.get('catalog_item_id') || !params.get('starts_at')) { message.textContent = 'Pilih konsol dan jadwal terlebih dahulu.'; return; }
            message.textContent = 'Memeriksa jadwal…';
            try {
                const response = await fetch(form.dataset.availability + '?' + params, { signal: controller.signal, credentials: 'same-origin', headers: { Accept: 'application/json' }, cache: 'no-store' });
                const data = await response.json();
                if (!response.ok) throw new Error(Object.values(data.errors || {}).flat()[0] || 'Jadwal belum dapat diperiksa. Coba lagi.');
                document.querySelector('#rs-price').textContent = money(data.total_price);
                document.querySelector('#rs-end').textContent = data.ends_at;
                document.querySelector('#rs-stock').textContent = data.available + ' unit';
                message.textContent = data.available > 0 ? 'Unit tersedia untuk rentang waktu ini. Permintaan masih memerlukan konfirmasi admin.' : 'Semua unit terisi pada jadwal ini. Pilih waktu lain.';
                button.disabled = data.available < 1;
            } catch (error) { if (error.name !== 'AbortError') { message.textContent = error.message; document.querySelector('#rs-stock').textContent = 'Belum terverifikasi'; } }
        };
        mode.addEventListener('change', () => {
            const values = mode.value === 'daily' ? [1,2,3,4,5,6,7] : [30,60,90,120,180,240,360,480];
            duration.replaceChildren(...values.map(value => { const o = document.createElement('option'); o.value = value; o.textContent = value + (mode.value === 'daily' ? ' hari' : ' menit'); return o; }));
            duration.value = mode.value === 'daily' ? '1' : '60'; check();
        });
        ['catalog_item_id','duration','starts_at'].forEach(name => form.elements[name].addEventListener('change', check));
        form.addEventListener('submit', () => { button.disabled = true; button.textContent = 'Mengirim reservasi…'; });
        check();
    }
    document.querySelectorAll('form[data-confirm]').forEach(form => form.addEventListener('submit', event => { if (!confirm(form.dataset.confirm)) event.preventDefault(); }));
    const rental = document.querySelector('#rs-live-rental');
    if (rental) {
        let left = 0, initial = 0, running = false, anchor = performance.now();
        const clock = rental.querySelector('[data-rs-clock]');
        const render = () => { if (running) left = Math.max(0, initial - Math.floor((performance.now() - anchor) / 1000)); clock.textContent = [Math.floor(left/3600),Math.floor(left%3600/60),left%60].map(v => String(v).padStart(2,'0')).join(':'); };
        let loading = false;
        const sync = async () => {
            if (loading || document.hidden) return; loading = true;
            try {
                const response = await fetch(rental.dataset.statusUrl, { credentials: 'same-origin', cache: 'no-store', headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error('Status belum dapat diperbarui. Coba muat ulang atau masuk kembali.');
                const data = await response.json();
                initial = data.remaining; left = initial; running = data.running; anchor = performance.now();
                rental.querySelector('[data-rs-status]').textContent = data.label;
                rental.querySelector('[data-rs-price]').textContent = data.price;
                rental.querySelector('[data-rs-duration]').textContent = data.duration_minutes;
                rental.querySelector('[data-rs-end]').textContent = data.ends_at;
                rental.querySelector('[data-rs-hint]').textContent = data.status === 'playing' ? (data.running ? 'Timer sedang berjalan.' : 'Timer dijeda oleh admin.') : (data.status === 'completed' ? 'Sesi sudah selesai. Terima kasih sudah bermain.' : 'Timer muncul setelah admin memulai sesi.');
                if (data.status === 'playing' && data.remaining === 0) rental.querySelector('[data-rs-hint]').textContent = 'Waktu habis. Hubungi admin untuk menyelesaikan sesi.';
                const cancelForm = rental.querySelector('[data-rs-cancel]'); if (cancelForm) cancelForm.hidden = !['pending','confirmed'].includes(data.status);
                const history = rental.querySelector('[data-rs-extensions]');
                if (history) {
                    const list = history.querySelector('[data-rs-extension-list]');
                    history.hidden = !data.extensions?.length;
                    list.replaceChildren(...(data.extensions || []).slice().reverse().map(item => {
                        const row = document.createElement('p'); row.textContent = '+' + item.minutes + ' menit ';
                        const badge = document.createElement('span'); badge.className = 'rs-tag'; badge.textContent = ({pending:'Menunggu admin',approved:'Disetujui',rejected:'Ditolak'})[item.status] || item.status;
                        row.append(badge); return row;
                    }));
                }
                const more = rental.querySelector('.rs-extension'); if (more) more.hidden = data.status !== 'playing' || data.remaining === 0 || data.extension_pending;
                rental.querySelector('[data-rs-connection]').textContent = data.extension_pending ? 'Permintaan tambahan waktu sedang menunggu admin.' : 'Terhubung. Status diperbarui otomatis.';
                render();
            } catch(error) { rental.querySelector('[data-rs-connection]').textContent = error.message; }
            finally { loading = false; }
        };
        sync(); const refresh = setInterval(sync, 5000), ticker = setInterval(render, 1000);
        document.addEventListener('visibilitychange', () => { if (!document.hidden) sync(); });
        window.addEventListener('pagehide', () => { clearInterval(refresh); clearInterval(ticker); });
        window.addEventListener('pageshow', event => { if (event.persisted) location.reload(); });
    }
})();
