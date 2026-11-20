(() => {
    const money = new Intl.NumberFormat('id-ID');
    const formatRp = (val) => 'Rp ' + money.format(Math.max(0, Number(val) || 0));

    // ==================== ACCOUNT DROPDOWN & MOBILE NAV ====================
    const accountBtn = document.querySelector('#ps-account-btn');
    const accountMenu = document.querySelector('#ps-account-menu');
    if (accountBtn && accountMenu) {
        accountBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = accountMenu.hasAttribute('hidden');
            if (isHidden) {
                accountMenu.removeAttribute('hidden');
                accountBtn.setAttribute('aria-expanded', 'true');
            } else {
                accountMenu.setAttribute('hidden', '');
                accountBtn.setAttribute('aria-expanded', 'false');
            }
        });
        document.addEventListener('click', (e) => {
            if (!accountMenu.contains(e.target) && e.target !== accountBtn) {
                accountMenu.setAttribute('hidden', '');
                accountBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    const mobileToggle = document.querySelector('#ps-mobile-toggle');
    const mobileDrawer = document.querySelector('#ps-mobile-drawer');
    if (mobileToggle && mobileDrawer) {
        mobileToggle.addEventListener('click', () => {
            const open = mobileDrawer.classList.toggle('is-open');
            mobileToggle.setAttribute('aria-expanded', String(open));
        });
        mobileDrawer.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                mobileDrawer.classList.remove('is-open');
                mobileToggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    // ==================== TOAST HELPER ====================
    const toastEl = document.querySelector('#ps-toast');
    let toastTimer = null;
    const showToast = (title, message) => {
        if (!toastEl) return;
        toastEl.innerHTML = `<strong>${title}</strong><div style="margin-top:4px;color:#bbf7d0">${message}</div>`;
        toastEl.removeAttribute('hidden');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toastEl.setAttribute('hidden', '');
        }, 6000);
    };

    // ==================== MODAL HELPERS ====================
    const closeAllCustomDropdowns = (exceptName = null) => {
        document.querySelectorAll('.ps-dropdown').forEach((dd) => {
            if (exceptName && dd.dataset.dropdown === exceptName) return;
            dd.classList.remove('is-open');
            const btn = dd.querySelector('.ps-dd-trigger');
            const menu = dd.querySelector('.ps-dd-menu');
            btn?.setAttribute('aria-expanded', 'false');
            menu?.setAttribute('hidden', '');
        });
    };

    const closeAllModals = () => {
        closeAllCustomDropdowns();
        document.querySelectorAll('.ps-modal-backdrop').forEach((m) => m.setAttribute('hidden', ''));
    };

    document.querySelectorAll('[data-close-modal]').forEach((btn) => {
        btn.addEventListener('click', closeAllModals);
    });

    document.querySelectorAll('.ps-modal-backdrop').forEach((backdrop) => {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) closeAllModals();
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.ps-dropdown')) {
            closeAllCustomDropdowns();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAllModals();
    });

    // ==================== BOOKING DATA CATALOG ====================
    const BOOKING_ITEMS = [
        { id: 'paket-ps4-reguler', group: 'Paket Rental', title: 'PAKET PS4 REGULER', sub: 'Rp 70.000 / Hari • 1 Unit PS4 Slim/Pro + 2 Stik + 10+ Game', hourlyRate: 12000, hours12Rate: 45000, dailyRate: 70000, defaultService: 'express', defaultDuration: '1d' },
        { id: 'paket-ps5-nextgen', group: 'Paket Rental', title: 'PAKET PS5 NEXT-GEN', sub: 'Rp 150.000 / Hari • 1 Unit PS5 Ultra HD + 2 DualSense + 15+ Game', hourlyRate: 25000, hours12Rate: 95000, dailyRate: 150000, defaultService: 'express', defaultDuration: '1d' },
        { id: 'paket-weekend', group: 'Paket Rental', title: 'PAKET WEEKEND', sub: 'Rp 200.000 / 3 Hari • Bebas PS4/PS5 • 72 Jam Non-Stop + Snack', hourlyRate: 20000, hours12Rate: 50000, dailyRate: 75000, fixedRates: { '3d': 200000 }, defaultService: 'express', defaultDuration: '3d' },
        { id: 'paket-mingguan-pro', group: 'Paket Rental', title: 'PAKET MINGGUAN PRO', sub: 'Rp 450.000 / 7 Hari • Full Set PS4/PS5 • Bebas Tukar Game 2x', hourlyRate: 20000, hours12Rate: 45000, dailyRate: 70000, fixedRates: { '7d': 450000 }, defaultService: 'express', defaultDuration: '7d' },

        { id: 'ps5-vip-01', group: 'Unit PlayStation 5', title: 'PS5 Disc Edition Ultra (UNIT VIP-01)', sub: 'Rp 25.000 / jam • 4K 120Hz Ray Tracing • 2 DualSense Wireless', hourlyRate: 25000, hours12Rate: 110000, dailyRate: 150000, defaultService: 'reguler', defaultDuration: '2h' },
        { id: 'ps5-reg-02', group: 'Unit PlayStation 5', title: 'PS5 Digital Slim Edition (UNIT REG-02)', sub: 'Rp 22.000 / jam • 4K HDR Display • 2 DualSense (White & Black)', hourlyRate: 22000, hours12Rate: 95000, dailyRate: 135000, defaultService: 'reguler', defaultDuration: '2h' },
        { id: 'ps5-vip-03', group: 'Unit PlayStation 5', title: 'PS5 Party 4-Player VIP (UNIT VIP-03)', sub: 'Rp 35.000 / jam • 4 Stik DualSense • Sofa Private 4-6 Orang', hourlyRate: 35000, hours12Rate: 160000, dailyRate: 220000, isVipUnit: true, defaultService: 'vip', defaultDuration: '2h' },

        { id: 'ps4-reg-04', group: 'Unit PlayStation 4', title: 'PS4 Pro 1TB 4K Enhanced (UNIT REG-04)', sub: 'Rp 15.000 / jam • Dynamic 4K Boost • 2 DualShock 4 Original', hourlyRate: 15000, hours12Rate: 65000, dailyRate: 90000, defaultService: 'reguler', defaultDuration: '2h' },
        { id: 'ps4-reg-05', group: 'Unit PlayStation 4', title: 'PS4 Slim 500GB Standard (UNIT REG-05)', sub: 'Rp 12.000 / jam • Silent Cooling Full HD • 2 DualShock 4', hourlyRate: 12000, hours12Rate: 50000, dailyRate: 70000, defaultService: 'reguler', defaultDuration: '2h' },
        { id: 'ps4-reg-06', group: 'Unit PlayStation 4', title: 'PS4 Standard Classic Unit (UNIT REG-06)', sub: 'Rp 10.000 / jam • 1080p Full HD • TV LED 43" + 2 DualShock 4', hourlyRate: 10000, hours12Rate: 45000, dailyRate: 60000, defaultService: 'reguler', defaultDuration: '2h' },

        { id: 'nintendo-01', group: 'Unit Nintendo & PS3', title: 'Nintendo 01 (Switch OLED White)', sub: 'Rp 20.000 / jam • 2 Joy-Con + Pro Controller • Zelda & Mario Kart', hourlyRate: 20000, hours12Rate: 85000, dailyRate: 120000, defaultService: 'reguler', defaultDuration: '2h' },
        { id: 'nintendo-02', group: 'Unit Nintendo & PS3', title: 'Nintendo 02 (Switch OLED Dock & Portable)', sub: 'Rp 20.000 / jam • TV Dock Mode & Portable • Pokemon & Overcooked', hourlyRate: 20000, hours12Rate: 85000, dailyRate: 120000, defaultService: 'reguler', defaultDuration: '2h' },
        { id: 'nintendo-03', group: 'Unit Nintendo & PS3', title: 'Nintendo 03 (Switch OLED Party Edition)', sub: 'Rp 20.000 / jam • 4 Joy-Con Party Edition • Mario Party & Just Dance', hourlyRate: 20000, hours12Rate: 90000, dailyRate: 130000, defaultService: 'reguler', defaultDuration: '2h' },

        { id: 'ps3-01', group: 'Unit Nintendo & PS3', title: 'PS3 01 (PlayStation 3 Slim)', sub: 'Rp 10.000 / jam • 500GB HDD • 2 Stik DualShock 3 Wireless', hourlyRate: 10000, hours12Rate: 40000, dailyRate: 55000, defaultService: 'reguler', defaultDuration: '2h' },
        { id: 'ps3-02', group: 'Unit Nintendo & PS3', title: 'PS3 02 (PlayStation 3 Action Pack)', sub: 'Rp 10.000 / jam • 500GB HDD • Full Game Action, Adventure, Racing', hourlyRate: 10000, hours12Rate: 40000, dailyRate: 55000, defaultService: 'reguler', defaultDuration: '2h' },
        { id: 'ps3-03', group: 'Unit Nintendo & PS3', title: 'PS3 03 (PlayStation 3 Retro & Sport)', sub: 'Rp 10.000 / jam • 500GB HDD • Full Game Retro & Sport Series', hourlyRate: 10000, hours12Rate: 40000, dailyRate: 55000, defaultService: 'reguler', defaultDuration: '2h' },
    ];

    const SERVICE_METHODS = [
        { id: 'reguler', mode: 'onsite', title: 'Booking & Main di Tempat (Reguler Lounge)', sub: 'Main di area reguler lounge ber-AC & TV 4K HDR (Durasi per Jam)' },
        { id: 'vip', mode: 'onsite', title: 'Booking & Main di Tempat (VIP Private Room)', sub: 'Ruangan privat kedap suara & sofa VIP (+Rp 10rb/jam, Durasi per Jam)' },
        { id: 'express', mode: 'takehome', title: 'Antar-Jemput Express (Sewa Bawa ke Rumah)', sub: 'Diantar & dipasang teknisi ke rumah/kos (Durasi 12 Jam – Harian)' },
        { id: 'outlet', mode: 'takehome', title: 'Ambil di Outlet (Sewa Bawa ke Rumah)', sub: 'Ambil & kembalikan unit sendiri di outlet (Durasi 12 Jam – Harian)' },
    ];

    const ONSITE_DURATIONS = [
        { id: '1h', title: '1 Jam', sub: 'Sesi singkat 1 jam main di tempat', hours: 1 },
        { id: '2h', title: '2 Jam', sub: 'Sesi mabar standar 2 jam di tempat', hours: 2 },
        { id: '3h', title: '3 Jam', sub: 'Paket populer mabar 3 jam', hours: 3 },
        { id: '4h', title: '4 Jam', sub: 'Sesi turnamen mini 4 jam', hours: 4 },
        { id: '5h', title: '5 Jam', sub: 'Sesi maraton 5 jam nonstop', hours: 5 },
        { id: '6h', title: '6 Jam', sub: 'Paket puas setengah hari 6 jam di lounge', hours: 6 },
        { id: '8h', title: '8 Jam', sub: 'Full session 8 jam di tempat', hours: 8 },
        { id: '10h', title: '10 Jam', sub: 'Sesi komunitas / begadang 10 jam', hours: 10 },
    ];

    const TAKEHOME_DURATIONS = [
        { id: '12h', title: '12 Jam', sub: 'Sewa bawa ke rumah setengah hari (12 jam)', type: '12h', factor: 1 },
        { id: '1d', title: '1 Hari (24 Jam)', sub: 'Sewa harian penuh 24 jam bawa ke rumah', type: 'days', days: 1 },
        { id: '2d', title: '2 Hari (48 Jam)', sub: 'Sewa 2 hari penuh bawa ke rumah', type: 'days', days: 2 },
        { id: '3d', title: '3 Hari (72 Jam / Weekend)', sub: 'Paket maraton 3 hari / weekend 72 jam', type: 'days', days: 3 },
        { id: '4d', title: '4 Hari (96 Jam)', sub: 'Sewa 4 hari penuh bawa ke rumah', type: 'days', days: 4 },
        { id: '5d', title: '5 Hari (120 Jam)', sub: 'Sewa 5 hari ekstra puas di rumah', type: 'days', days: 5 },
        { id: '7d', title: '7 Hari (1 Minggu)', sub: 'Paket mingguan pro 7 hari penuh', type: 'days', days: 7 },
        { id: '14d', title: '14 Hari (2 Minggu)', sub: 'Paket sewa 2 minggu penuh di rumah', type: 'days', days: 14 },
        { id: '30d', title: '30 Hari (1 Bulan)', sub: 'Paket sewa bulanan full set', type: 'days', days: 30 },
    ];

    const SCHEDULES = [
        { id: 'now', title: 'Sekarang / Langsung Mulai (Instant Ready)', sub: 'Unit langsung disiapkan di lounge atau langsung dikirim teknisi' },
        { id: '08:00', title: 'Pagi — 08:00 WIB', sub: 'Slot pagi awal • Pengantaran atau buka station pukul 08:00 WIB' },
        { id: '10:00', title: 'Pagi — 10:00 WIB', sub: 'Slot pagi menjelang siang • Cocok untuk mabar santai pagi' },
        { id: '12:00', title: 'Siang — 12:00 WIB', sub: 'Slot tengah hari • Siap main istirahat siang / weekend' },
        { id: '14:00', title: 'Siang — 14:00 WIB', sub: 'Slot siang favorit • Ruangan dingin & unit sudah terkalibrasi' },
        { id: '16:00', title: 'Sore — 16:00 WIB', sub: 'Slot sore sepulang sekolah/kerja • Waktu mabar paling laris' },
        { id: '18:30', title: 'Petang — 18:30 WIB', sub: 'Slot awal malam • Siap untuk sesi mabar malam atau antar rumah' },
        { id: '20:00', title: 'Malam — 20:00 WIB (Prime Time Mabar)', sub: 'Slot malam utama • Layanan penuh & F&B lounge siap pesan' },
        { id: '22:00', title: 'Malam — 22:00 WIB (Night Owl Session)', sub: 'Slot begadang malam • Operasional 24 jam non-stop' },
        { id: '00:00', title: 'Dini Hari — 00:00 WIB (Midnight Gaming)', sub: 'Slot tengah malam • Tenang & nyaman untuk sesi maraton' },
        { id: 'tomorrow-09', title: 'Besok Pagi — 09:00 WIB (Pre-Order Besok)', sub: 'Jadwalkan reservasi atau pengantaran untuk besok pagi' },
        { id: 'weekend-fri', title: 'Akhir Pekan — Jumat 17:00 WIB (Weekend Start)', sub: 'Slot khusus mulai paket weekend hingga Senin pagi' },
    ];

    const GAMES_CATALOG = [
        { id: 'fc24', title: 'EA Sports FC 24 (Update Transfer Terbaru)', sub: 'Sports Football • 1-4 Pemain • PS4 & PS5' },
        { id: 'pes24', title: 'eFootball PES 2024 Patch Full Liga & Timnas', sub: 'Sports Football • 1-4 Pemain • PS3, PS4 & PS5' },
        { id: 'spiderman2', title: "Marvel's Spider-Man 2", sub: 'Action Adventure Open World • 1 Pemain • PS5' },
        { id: 'gow', title: 'God of War Ragnarök', sub: 'Action Adventure Epic Story • 1 Pemain • PS4 & PS5' },
        { id: 'tekken8', title: 'Tekken 8 Deluxe Edition', sub: '3D Fighting Tournament • 1-2 Pemain • PS5' },
        { id: 'mk1', title: 'Mortal Kombat 1 (MK 1) Full Roster', sub: 'Fighting Arcade • 1-2 Pemain • PS5 & Switch' },
        { id: 'gtav', title: 'Grand Theft Auto V (GTA V) Premium Edition', sub: 'Open World Action • 1 Pemain • PS3, PS4 & PS5' },
        { id: 'rdr2', title: 'Red Dead Redemption 2', sub: 'Open World Western Story • 1 Pemain • PS4 & PS5' },
        { id: 'nba2k24', title: 'NBA 2K24 Kobe Bryant Edition', sub: 'Sports Basketball • 1-4 Pemain • PS4, PS5 & Switch' },
        { id: 'gt7', title: 'Gran Turismo 7 (Support Racing Wheel)', sub: 'Racing Simulator 4K • 1-2 Pemain • PS4 & PS5' },
        { id: 'nfs', title: 'Need for Speed Heat & Unbound', sub: 'Street Racing Arcade • 1-2 Pemain • PS4 & PS5' },
        { id: 'coop-party', title: 'Overcooked! All You Can Eat & It Takes Two', sub: 'Co-op Party & Family • 2-4 Pemain • Semua Konsol' },
        { id: 'wwe-ufc', title: 'WWE 2K24 & UFC 5 Fighting Pack', sub: 'Fighting Sports • 1-4 Pemain • PS4 & PS5' },
        { id: 'rpg-pack', title: 'Hogwarts Legacy & Elden Ring', sub: 'Open World Fantasy RPG • 1 Pemain • PS4 & PS5' },
        { id: 'tsushima', title: "Ghost of Tsushima Director's Cut", sub: 'Samurai Open World • 1 Pemain • PS4 & PS5' },
        { id: 'anime-kart', title: 'Naruto Storm Connections & CTR Nitro-Fueled', sub: 'Anime Battle & Kart Racing • 1-4 Pemain • PS4 & PS5' },
        { id: 'switch-pack', title: 'Mario Kart 8 Deluxe + Zelda TOTK + Smash Bros', sub: 'Nintendo Switch Exclusive • 1-4 Pemain • Switch OLED' },
    ];

    // ==================== BOOKING MODAL STATE & RENDERER ====================
    const bookingModal = document.querySelector('#ps-booking-modal');
    const bookingForm = document.querySelector('#ps-booking-form');
    const bookingTitleEl = document.querySelector('#ps-booking-title');
    const bookingSubtitleEl = document.querySelector('#ps-booking-subtitle');
    const bookingTotalEl = document.querySelector('#ps-booking-total');
    const bookingSummaryNoteEl = document.querySelector('#ps-booking-summary-note');
    const bookingUnitLabelEl = document.querySelector('#ps-booking-unit-label');

    const state = {
        itemId: 'paket-ps5-nextgen',
        serviceId: 'express',
        durationId: '1d',
        scheduleId: 'now',
        gameIds: ['fc24', 'spiderman2'],
    };

    const getSelectedItem = () => BOOKING_ITEMS.find((x) => x.id === state.itemId) || BOOKING_ITEMS[0];
    const getSelectedService = () => SERVICE_METHODS.find((x) => x.id === state.serviceId) || SERVICE_METHODS[0];
    const getDurationList = () => (getSelectedService().mode === 'onsite' ? ONSITE_DURATIONS : TAKEHOME_DURATIONS);
    const getSelectedDuration = () => {
        const list = getDurationList();
        return list.find((d) => d.id === state.durationId) || list[0];
    };
    const getSelectedSchedule = () => SCHEDULES.find((s) => s.id === state.scheduleId) || SCHEDULES[0];

    const calculateTotal = () => {
        const item = getSelectedItem();
        const service = getSelectedService();
        const dur = getSelectedDuration();

        if (service.mode === 'onsite') {
            const vipExtra = service.id === 'vip' && !item.isVipUnit ? 10000 : 0;
            return (item.hourlyRate + vipExtra) * (dur.hours || 1);
        }

        if (item.fixedRates && item.fixedRates[dur.id]) {
            return item.fixedRates[dur.id];
        }
        if (dur.type === '12h') {
            return item.hours12Rate;
        }
        return item.dailyRate * (dur.days || 1);
    };

    const checkIconSvg = '<svg class="ps-dd-check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';

    const renderItemMenu = () => {
        const menu = document.querySelector('#ps-dd-item-menu');
        const titleEl = document.querySelector('#ps-dd-item-title');
        const subEl = document.querySelector('#ps-dd-item-sub');
        const selected = getSelectedItem();
        if (titleEl) titleEl.textContent = selected.title;
        if (subEl) subEl.textContent = selected.sub;
        if (!menu) return;

        let lastGroup = '';
        menu.innerHTML = BOOKING_ITEMS.map((item) => {
            const groupHeader = item.group !== lastGroup
                ? `<div class="ps-dd-group-label">${item.group}</div>`
                : '';
            lastGroup = item.group;
            const active = item.id === state.itemId;
            return `${groupHeader}<button type="button" class="ps-dd-option ${active ? 'is-selected' : ''}" role="option" aria-selected="${active}" data-select-item="${item.id}">
                <span class="ps-dd-opt-text"><strong>${item.title}</strong><small>${item.sub}</small></span>
                ${active ? checkIconSvg : ''}
            </button>`;
        }).join('');

        menu.querySelectorAll('[data-select-item]').forEach((opt) => {
            opt.addEventListener('click', (e) => {
                e.stopPropagation();
                state.itemId = opt.dataset.selectItem;
                const chosen = getSelectedItem();
                if (chosen.defaultService) {
                    state.serviceId = chosen.defaultService;
                }
                if (chosen.defaultDuration) {
                    state.durationId = chosen.defaultDuration;
                }
                closeAllCustomDropdowns();
                renderBookingUI();
            });
        });
    };

    const renderServiceMenu = () => {
        const menu = document.querySelector('#ps-dd-service-menu');
        const titleEl = document.querySelector('#ps-dd-service-title');
        const subEl = document.querySelector('#ps-dd-service-sub');
        const selected = getSelectedService();
        if (titleEl) titleEl.textContent = selected.title;
        if (subEl) subEl.textContent = selected.sub;
        if (!menu) return;

        menu.innerHTML = SERVICE_METHODS.map((srv) => {
            const active = srv.id === state.serviceId;
            return `<button type="button" class="ps-dd-option ${active ? 'is-selected' : ''}" role="option" aria-selected="${active}" data-select-service="${srv.id}">
                <span class="ps-dd-opt-text"><strong>${srv.title}</strong><small>${srv.sub}</small></span>
                ${active ? checkIconSvg : ''}
            </button>`;
        }).join('');

        menu.querySelectorAll('[data-select-service]').forEach((opt) => {
            opt.addEventListener('click', (e) => {
                e.stopPropagation();
                const prevMode = getSelectedService().mode;
                state.serviceId = opt.dataset.selectService;
                const nextMode = getSelectedService().mode;
                if (prevMode !== nextMode) {
                    state.durationId = nextMode === 'onsite' ? '2h' : '1d';
                }
                closeAllCustomDropdowns();
                renderBookingUI();
            });
        });
    };

    const renderDurationMenu = () => {
        const menu = document.querySelector('#ps-dd-duration-menu');
        const titleEl = document.querySelector('#ps-dd-duration-title');
        const subEl = document.querySelector('#ps-dd-duration-sub');
        const list = getDurationList();
        const selected = getSelectedDuration();
        state.durationId = selected.id;
        const service = getSelectedService();

        if (bookingUnitLabelEl) {
            bookingUnitLabelEl.textContent = service.mode === 'onsite'
                ? 'Durasi Main di Tempat (Jam)'
                : 'Durasi Sewa Bawa Pulang (12 Jam – Harian)';
        }
        if (titleEl) titleEl.textContent = selected.title;
        if (subEl) subEl.textContent = selected.sub;
        if (!menu) return;

        menu.innerHTML = list.map((dur) => {
            const active = dur.id === state.durationId;
            return `<button type="button" class="ps-dd-option ${active ? 'is-selected' : ''}" role="option" aria-selected="${active}" data-select-duration="${dur.id}">
                <span class="ps-dd-opt-text"><strong>${dur.title}</strong><small>${dur.sub}</small></span>
                ${active ? checkIconSvg : ''}
            </button>`;
        }).join('');

        menu.querySelectorAll('[data-select-duration]').forEach((opt) => {
            opt.addEventListener('click', (e) => {
                e.stopPropagation();
                state.durationId = opt.dataset.selectDuration;
                closeAllCustomDropdowns();
                renderBookingUI();
            });
        });
    };

    const renderScheduleMenu = () => {
        const menu = document.querySelector('#ps-dd-schedule-menu');
        const titleEl = document.querySelector('#ps-dd-schedule-title');
        const subEl = document.querySelector('#ps-dd-schedule-sub');
        const selected = getSelectedSchedule();
        if (titleEl) titleEl.textContent = selected.title;
        if (subEl) subEl.textContent = selected.sub;
        if (!menu) return;

        menu.innerHTML = SCHEDULES.map((sch) => {
            const active = sch.id === state.scheduleId;
            return `<button type="button" class="ps-dd-option ${active ? 'is-selected' : ''}" role="option" aria-selected="${active}" data-select-schedule="${sch.id}">
                <span class="ps-dd-opt-text"><strong>${sch.title}</strong><small>${sch.sub}</small></span>
                ${active ? checkIconSvg : ''}
            </button>`;
        }).join('');

        menu.querySelectorAll('[data-select-schedule]').forEach((opt) => {
            opt.addEventListener('click', (e) => {
                e.stopPropagation();
                state.scheduleId = opt.dataset.selectSchedule;
                closeAllCustomDropdowns();
                renderBookingUI();
            });
        });
    };

    const renderGamesMenu = () => {
        const menu = document.querySelector('#ps-dd-games-menu');
        const titleEl = document.querySelector('#ps-dd-games-title');
        const subEl = document.querySelector('#ps-dd-games-sub');
        const selectedGames = GAMES_CATALOG.filter((g) => state.gameIds.includes(g.id));

        if (titleEl) {
            if (selectedGames.length === 0) {
                titleEl.textContent = 'Pilih Request Game (Opsional)';
            } else if (selectedGames.length <= 2) {
                titleEl.textContent = selectedGames.map((g) => g.title.split(' (')[0]).join(', ');
            } else {
                const firstTwo = selectedGames.slice(0, 2).map((g) => g.title.split(' (')[0]).join(', ');
                titleEl.textContent = `${firstTwo} (+${selectedGames.length - 2} lainnya)`;
            }
        }
        if (subEl) {
            subEl.textContent = selectedGames.length > 0
                ? `${selectedGames.length} game dipilih • Klik untuk tambah/ubah pilihan game`
                : 'Klik untuk memilih game lengkap dari katalog kami';
        }
        if (!menu) return;

        menu.innerHTML = GAMES_CATALOG.map((game) => {
            const active = state.gameIds.includes(game.id);
            return `<button type="button" class="ps-dd-option ${active ? 'is-selected' : ''}" role="option" aria-selected="${active}" data-toggle-game="${game.id}">
                <span class="ps-dd-opt-text"><strong>${game.title}</strong><small>${game.sub}</small></span>
                ${active ? checkIconSvg : ''}
            </button>`;
        }).join('');

        menu.querySelectorAll('[data-toggle-game]').forEach((opt) => {
            opt.addEventListener('click', (e) => {
                e.stopPropagation();
                const gid = opt.dataset.toggleGame;
                if (state.gameIds.includes(gid)) {
                    state.gameIds = state.gameIds.filter((id) => id !== gid);
                } else {
                    state.gameIds = [...state.gameIds, gid];
                }
                renderGamesMenu();
            });
        });
    };

    const renderBookingUI = () => {
        renderItemMenu();
        renderServiceMenu();
        renderDurationMenu();
        renderScheduleMenu();
        renderGamesMenu();

        const total = calculateTotal();
        const item = getSelectedItem();
        const service = getSelectedService();
        const dur = getSelectedDuration();
        if (bookingTotalEl) {
            bookingTotalEl.textContent = formatRp(total);
        }
        if (bookingSummaryNoteEl) {
            bookingSummaryNoteEl.textContent = `${item.title} • ${service.title.split(' (')[0]} • ${dur.title}`;
        }
    };

    // Toggle open/close on each custom dropdown trigger
    document.querySelectorAll('.ps-dropdown').forEach((dd) => {
        const trigger = dd.querySelector('.ps-dd-trigger');
        const menu = dd.querySelector('.ps-dd-menu');
        const name = dd.dataset.dropdown;
        if (!trigger || !menu) return;

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = dd.classList.contains('is-open');
            closeAllCustomDropdowns(name);
            if (isOpen) {
                dd.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
                menu.setAttribute('hidden', '');
            } else {
                dd.classList.add('is-open');
                trigger.setAttribute('aria-expanded', 'true');
                menu.removeAttribute('hidden');
            }
        });
    });

    const openBookingModal = ({ itemId, title, subtitle, serviceId, durationId, scheduleId }) => {
        if (!bookingModal) return;
        if (itemId && BOOKING_ITEMS.some((x) => x.id === itemId)) {
            state.itemId = itemId;
        }
        const chosen = getSelectedItem();
        state.serviceId = serviceId || chosen.defaultService || 'express';
        state.durationId = durationId || chosen.defaultDuration || (getSelectedService().mode === 'onsite' ? '2h' : '1d');
        state.scheduleId = scheduleId || 'now';

        if (bookingTitleEl) bookingTitleEl.textContent = title || 'Reservasi Rental';
        if (bookingSubtitleEl) bookingSubtitleEl.textContent = subtitle || 'Klik setiap kolom untuk memilih opsi lengkap.';

        closeAllCustomDropdowns();
        renderBookingUI();
        bookingModal.removeAttribute('hidden');
    };

    document.querySelectorAll('[data-open-booking]').forEach((btn) => {
        btn.addEventListener('click', () => {
            openBookingModal({
                itemId: btn.dataset.itemId || 'paket-ps5-nextgen',
                title: btn.dataset.title || 'Reservasi Unit / Paket',
                subtitle: btn.dataset.subtitle || 'Pilih unit, layanan, durasi, jadwal, dan game favorit Anda.',
                serviceId: btn.dataset.defaultService || null,
                durationId: btn.dataset.defaultDuration || null,
                scheduleId: btn.dataset.defaultSchedule || null,
            });
        });
    });

    if (bookingForm) {
        bookingForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const item = getSelectedItem();
            const service = getSelectedService();
            const dur = getSelectedDuration();
            const sch = getSelectedSchedule();
            const games = GAMES_CATALOG.filter((g) => state.gameIds.includes(g.id)).map((g) => g.title.split(' (')[0]);
            const totalText = formatRp(calculateTotal());

            const bookings = JSON.parse(localStorage.getItem('ps_seleraku_bookings') || '[]');
            bookings.unshift({
                item: item.title,
                service: service.title,
                duration: dur.title,
                schedule: sch.title,
                games,
                total: totalText,
                createdAt: new Date().toISOString(),
            });
            localStorage.setItem('ps_seleraku_bookings', JSON.stringify(bookings.slice(0, 10)));
            closeAllModals();
            showToast(
                'Pesanan Berhasil Dicatat!',
                `${item.title} (${dur.title}) • ${service.title.split(' (')[0]} • Jadwal: ${sch.title.split(' (')[0]} • Estimasi ${totalText}`
            );
        });
    }

    // ==================== UNIT DETAIL MODAL (PAGE KE2) ====================
    const detailModal = document.querySelector('#ps-detail-modal');
    const detailTitle = document.querySelector('#ps-detail-title');
    const detailMeta = document.querySelector('#ps-detail-meta');
    const detailSpecs = document.querySelector('#ps-detail-specs');
    const detailGames = document.querySelector('#ps-detail-games');
    const detailRate = document.querySelector('#ps-detail-rate');
    const detailBookBtn = document.querySelector('#ps-detail-book-btn');

    let detailUnitPayload = null;

    document.querySelectorAll('[data-open-detail]').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!detailModal) return;
            const games = (btn.dataset.games || '').split('|').filter(Boolean);
            detailUnitPayload = {
                itemId: btn.dataset.itemId || 'ps5-vip-01',
                title: `Booking ${btn.dataset.name}`,
                subtitle: `${btn.dataset.code || ''} • ${btn.dataset.statusLabel || ''}`,
            };
            if (detailTitle) detailTitle.textContent = btn.dataset.name || 'Detail Unit';
            if (detailMeta) detailMeta.textContent = `${btn.dataset.code || ''} • ${btn.dataset.platform || ''} • ${btn.dataset.statusLabel || ''}`;
            if (detailSpecs) detailSpecs.textContent = btn.dataset.specs || '';
            if (detailRate) detailRate.textContent = `${formatRp(btn.dataset.rate || 0)} / jam`;
            if (detailGames) {
                detailGames.innerHTML = games.map((g) => `<span class="ps-detail-tag">${g}</span>`).join('');
            }
            detailModal.removeAttribute('hidden');
        });
    });

    if (detailBookBtn) {
        detailBookBtn.addEventListener('click', () => {
            if (!detailUnitPayload) return;
            closeAllModals();
            openBookingModal(detailUnitPayload);
        });
    }

    // ==================== LEGAL / SUPPORT INFO MODAL ====================
    const infoModal = document.querySelector('#ps-info-modal');
    const infoTitle = document.querySelector('#ps-info-title');
    const infoBody = document.querySelector('#ps-info-body');
    const infoMap = {
        ketentuan: {
            title: 'Ketentuan Sewa PS Seleraku',
            body: '1. Penyewa wajib menunjukkan identitas aktif (KTP/SIM/Kartu Pelajar) saat serah terima unit.\n2. Kerusakan akibat kelalaian penggunaan atau cairan menjadi tanggung jawab penyewa.\n3. Keterlambatan pengembalian di atas 30 menit dikenakan tarif per jam tambahan.',
        },
        privasi: {
            title: 'Kebijakan Privasi',
            body: 'Data akun dan riwayat pemesanan Anda di PS Seleraku hanya digunakan untuk keperluan verifikasi penyewaan, pengantaran unit, dan layanan pelanggan internal.',
        },
        bantuan: {
            title: 'Bantuan & Support 24 Jam',
            body: 'Butuh bantuan setup TV/HDMI, request instalasi game baru, atau kendala teknis stik? Hubungi Concierge kami di WhatsApp +62 812-8899-PS55 atau email concierge@psseleraku.id.',
        },
    };

    document.querySelectorAll('[data-info-modal]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const entry = infoMap[btn.dataset.infoModal];
            if (!entry || !infoModal) return;
            if (infoTitle) infoTitle.textContent = entry.title;
            if (infoBody) infoBody.textContent = entry.body;
            infoModal.removeAttribute('hidden');
        });
    });

    // ==================== PAGE KE2 UNIT FILTERS ====================
    const unitCards = Array.from(document.querySelectorAll('[data-unit-card]'));
    if (unitCards.length > 0) {
        const searchInput = document.querySelector('#p2-search');
        const searchClear = document.querySelector('#p2-search-clear');
        const platformButtons = document.querySelectorAll('[data-platform-filter]');
        const priceSelect = document.querySelector('#p2-price');
        const statusSelect = document.querySelector('#p2-status');
        const countEl = document.querySelector('#p2-count');
        const emptyEl = document.querySelector('#p2-empty');
        const resetBtn = document.querySelector('#p2-reset-filters');

        let activePlatform = 'all';

        const applyUnitFilters = () => {
            const q = (searchInput?.value || '').trim().toLowerCase();
            const priceFilter = priceSelect?.value || 'all';
            const statusFilter = statusSelect?.value || 'all';

            if (searchClear) {
                searchClear.hidden = q.length === 0;
            }

            let visibleCount = 0;
            let readyCount = 0;

            unitCards.forEach((card) => {
                const platform = card.dataset.platform || '';
                const status = card.dataset.status || '';
                const rate = Number(card.dataset.rate || 0);
                const searchText = (card.dataset.search || '').toLowerCase();

                const matchesPlatform = activePlatform === 'all' || platform === activePlatform;
                const matchesSearch = !q || searchText.includes(q);
                const matchesStatus = statusFilter === 'all' || status === statusFilter;
                let matchesPrice = true;
                if (priceFilter === 'budget') matchesPrice = rate <= 15000;
                else if (priceFilter === 'mid') matchesPrice = rate > 15000 && rate <= 25000;
                else if (priceFilter === 'vip') matchesPrice = rate > 25000;

                const visible = matchesPlatform && matchesSearch && matchesStatus && matchesPrice;
                card.hidden = !visible;
                card.style.display = visible ? '' : 'none';
                if (visible) {
                    visibleCount++;
                    if (status === 'available') readyCount++;
                }
            });

            if (countEl) {
                countEl.textContent = `Menampilkan ${visibleCount} Unit Konsol (${readyCount} Siap Pakai)`;
            }
            if (emptyEl) {
                emptyEl.hidden = visibleCount > 0;
            }
        };

        platformButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                activePlatform = btn.dataset.platformFilter || 'all';
                platformButtons.forEach((other) => other.classList.toggle('is-active', other === btn));
                applyUnitFilters();
            });
        });

        ['input', 'search', 'keyup', 'change'].forEach((evt) => {
            searchInput?.addEventListener(evt, applyUnitFilters);
        });
        searchClear?.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }
            applyUnitFilters();
        });
        priceSelect?.addEventListener('change', applyUnitFilters);
        statusSelect?.addEventListener('change', applyUnitFilters);

        resetBtn?.addEventListener('click', () => {
            activePlatform = 'all';
            platformButtons.forEach((b) => b.classList.toggle('is-active', b.dataset.platformFilter === 'all'));
            if (searchInput) searchInput.value = '';
            if (priceSelect) priceSelect.value = 'all';
            if (statusSelect) statusSelect.value = 'all';
            applyUnitFilters();
        });

        applyUnitFilters();
    }
})();
