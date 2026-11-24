<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sewa Harian (Bawa Pulang) — PS SELERAKU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('seleraku-pages.css?v=logo-sync-1') }}">
    <script src="{{ asset('seleraku-pages.js?v=reservations-1') }}" defer></script>
</head>
<body class="ps-page-body" data-reservation-url="{{ route('reservations.create') }}">
    @include('partials.ps-navbar', ['activePage' => 'sewa-harian'])

    <main>
        <!-- HERO BANNER -->
        <section class="ps-hero-page2" aria-label="Hero Rental Harian PS Seleraku">
            <div class="ps-hero-inner">
                <h1 class="ps-hero-title">
                    RENTAL <span class="ps-text-ps4">PS4</span> &amp; <span class="ps-text-ps5">PS5</span>
                </h1>
                <p class="ps-hero-subtitle">
                    Nikmati pengalaman gaming terbaik tanpa harus membeli. Bawa pulang konsol favoritmu lengkap dengan stik wireless original, kabel HDMI 4K, dan koleksi game populer siap main.
                </p>
            </div>
        </section>

        <!-- KATALOG SEWA HARIAN (DESKTOP - 3) -->
        <section id="daily-catalog" class="ps-catalog-section ps-daily-section" aria-label="Katalog Sewa Harian Konsol">
            <div class="ps-container">
                <div class="ps-daily-head">
                    <div>
                        <h2 class="ps-daily-title">
                            <span>PILIH PLAYSTATION</span>
                            FAVORITMU.
                        </h2>
                        <p class="ps-daily-sub">
                            Pilih konsol yang ingin kamu sewa harian untuk dimainkan di rumah bersama teman atau keluarga dengan perangkat terawat, higienis, dan koleksi game terlengkap terinstal siap main.
                        </p>
                    </div>

                    <nav class="ps-mode-switcher" aria-label="Mode Tarif Sewa">
                        <a href="{{ route('page-ke2') }}" class="ps-mode-tab">
                            <span>Main di Lounge</span>
                            <small>Per Jam</small>
                        </a>
                        <a href="{{ route('sewa-harian') }}" class="ps-mode-tab is-active" aria-current="page">
                            <span>Sewa Harian</span>
                            <small>Bawa Pulang</small>
                        </a>
                    </nav>
                </div>

                <!-- FILTER BAR -->
                <div class="ps-daily-filter-panel">
                    <div class="ps-daily-filter-row">
                        <div class="ps-daily-search-wrap">
                            <svg class="ps-daily-search-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.3-4.3"/>
                            </svg>
                            <input
                                id="p2-search"
                                type="search"
                                placeholder="Cari konsol PlayStation atau judul game..."
                                aria-label="Cari konsol PlayStation atau judul game"
                            >
                            <button id="p2-search-clear" type="button" class="ps-daily-clear-btn" aria-label="Hapus pencarian" hidden>&times;</button>
                        </div>

                        <div class="ps-daily-platform-tabs" role="group" aria-label="Filter Platform">
                            <button type="button" class="ps-daily-plat-btn is-active" data-platform-filter="all">Semua</button>
                            <button type="button" class="ps-daily-plat-btn" data-platform-filter="ps5">PlayStation 5</button>
                            <button type="button" class="ps-daily-plat-btn" data-platform-filter="ps4">PlayStation 4</button>
                            <button type="button" class="ps-daily-plat-btn" data-platform-filter="ps3">PlayStation 3</button>
                            <button type="button" class="ps-daily-plat-btn" data-platform-filter="nintendo">Nintendo</button>
                        </div>

                        <div class="ps-daily-selects">
                            <select id="p2-price" data-price-mode="daily" class="ps-daily-select" aria-label="Filter Tarif Harian">
                                <option value="all">Semua Tarif</option>
                                <option value="budget">Hemat (&le; Rp 60rb/hari)</option>
                                <option value="mid">Menengah (Rp 90rb–120rb)</option>
                                <option value="vip">Premium (&ge; Rp 150rb/hari)</option>
                            </select>

                            <select id="p2-status" class="ps-daily-select" aria-label="Filter Status Unit">
                                <option value="all">Semua Status</option>
                                <option value="available">Tersedia</option>
                                <option value="in-use">Sedang Disewa</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <div class="ps-daily-meta-row">
                        <div class="ps-daily-count-wrap">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <rect x="3" y="3" width="7" height="7" rx="1"/>
                                <rect x="14" y="3" width="7" height="7" rx="1"/>
                                <rect x="3" y="14" width="7" height="7" rx="1"/>
                                <rect x="14" y="14" width="7" height="7" rx="1"/>
                            </svg>
                            <span id="p2-count">Menampilkan 8 Unit Konsol (5 Siap Pakai)</span>
                        </div>
                        <div class="ps-daily-legend" aria-label="Legenda Status Unit">
                            <span><i class="ps-dot-indicator is-green"></i> Hijau: Siap Main</span>
                            <span><i class="ps-dot-indicator is-red"></i> Merah: Sedang Disewa</span>
                            <span><i class="ps-dot-indicator is-slate"></i> Abu: Maintenance</span>
                        </div>
                    </div>
                </div>

                @php
                    $dailyUnits = [
                        [
                            'itemId' => 'daily-ps5-01',
                            'tag' => 'PS5 01',
                            'platformKey' => 'ps5',
                            'statusKey' => 'available',
                            'statusLabel' => 'Tersedia',
                            'statusClass' => 'is-available',
                            'rate' => 150000,
                            'rateText' => 'Rp 150.000',
                            'image' => 'images/daily-ps5-01.jpg',
                            'imgBadge' => '4K HDR',
                            'overlayNote' => null,
                            'name' => 'PlayStation 5 (PS5 01)',
                            'desc' => '2 Stik DualSense Wireless • 5 Game Terpopuler • Kabel HDMI Ultra High Speed.',
                            'chips' => ['2 DualSense', 'Ultra Fast SSD'],
                            'ctaText' => 'Sewa Harian',
                            'ctaIcon' => 'bag',
                            'defaultSchedule' => 'now',
                            'searchText' => 'ps5 01 playstation 5 dualsense 4k hdr ultra fast ssd spider-man fc24 harian',
                        ],
                        [
                            'itemId' => 'daily-ps5-02',
                            'tag' => 'PS5 02',
                            'platformKey' => 'ps5',
                            'statusKey' => 'in-use',
                            'statusLabel' => 'Sedang Disewa',
                            'statusClass' => 'is-rented',
                            'rate' => 150000,
                            'rateText' => 'Rp 150.000',
                            'image' => 'images/daily-ps5-02.jpg',
                            'imgBadge' => null,
                            'overlayNote' => 'Kembali 26 Okt',
                            'name' => 'PlayStation 5 (PS5 02)',
                            'desc' => '2 Stik DualSense Wireless + FIFA / EA FC 24 + God of War Ragnarok.',
                            'chips' => ['EA FC 24 Installed', '4K HDR'],
                            'ctaText' => 'Sedang Disewa',
                            'ctaIcon' => 'lock',
                            'defaultSchedule' => 'tomorrow-09',
                            'searchText' => 'ps5 02 playstation 5 slim ea fc 24 fifa god of war ragnarok 4k hdr',
                        ],
                        [
                            'itemId' => 'daily-ps4-01',
                            'tag' => 'PS4 01',
                            'platformKey' => 'ps4',
                            'statusKey' => 'available',
                            'statusLabel' => 'Tersedia',
                            'statusClass' => 'is-available',
                            'rate' => 90000,
                            'rateText' => 'Rp 90.000',
                            'image' => 'images/daily-ps4-01.jpg',
                            'imgBadge' => '1TB Pro',
                            'overlayNote' => null,
                            'name' => 'PlayStation 4 (PS4 01)',
                            'desc' => 'PS4 Pro 1TB • 2 DualShock 4 Wireless • PES / eFootball update terbaru.',
                            'chips' => ['PES 2024 Upd', 'DualShock 4'],
                            'ctaText' => 'Sewa Harian',
                            'ctaIcon' => 'bag',
                            'defaultSchedule' => 'now',
                            'searchText' => 'ps4 01 playstation 4 pro 1tb pes efootball dualshock 4',
                        ],
                        [
                            'itemId' => 'daily-ps4-02',
                            'tag' => 'PS4 02',
                            'platformKey' => 'ps4',
                            'statusKey' => 'maintenance',
                            'statusLabel' => 'Maintenance',
                            'statusClass' => 'is-maintenance',
                            'rate' => 90000,
                            'rateText' => 'Rp 90.000',
                            'image' => 'images/daily-ps4-02.jpg',
                            'imgBadge' => null,
                            'overlayNote' => 'Pengecekan Rutin',
                            'name' => 'PlayStation 4 (PS4 02)',
                            'desc' => 'Sedang pembaruan firmware & pengecekan hardware stik berkala.',
                            'chips' => ['Update Sistem', 'Kalibrasi Stik'],
                            'ctaText' => 'Maintenance',
                            'ctaIcon' => 'wrench',
                            'defaultSchedule' => 'tomorrow-09',
                            'searchText' => 'ps4 02 playstation 4 slim maintenance update sistem kalibrasi stik',
                        ],
                        [
                            'itemId' => 'daily-ps3-01',
                            'tag' => 'PS3 01',
                            'platformKey' => 'ps3',
                            'statusKey' => 'available',
                            'statusLabel' => 'Tersedia',
                            'statusClass' => 'is-available',
                            'rate' => 50000,
                            'rateText' => 'Rp 50.000',
                            'image' => 'images/daily-ps3-01.jpg',
                            'imgBadge' => '40+ Game Retro',
                            'overlayNote' => null,
                            'name' => 'PlayStation 3 (PS3 01)',
                            'desc' => 'PS3 Super Slim 500GB • 2 Stik DualShock 3 • 40+ Game Nostalgia (GTA V, Naruto, PES).',
                            'chips' => ['GTA V', 'PES Gembox'],
                            'ctaText' => 'Sewa Harian',
                            'ctaIcon' => 'bag',
                            'defaultSchedule' => 'now',
                            'searchText' => 'ps3 01 playstation 3 super slim gta v naruto pes gembox retro',
                        ],
                        [
                            'itemId' => 'daily-ps3-02',
                            'tag' => 'PS3 02',
                            'platformKey' => 'ps3',
                            'statusKey' => 'available',
                            'statusLabel' => 'Tersedia',
                            'statusClass' => 'is-available',
                            'rate' => 50000,
                            'rateText' => 'Rp 50.000',
                            'image' => 'images/ps3-unit.png',
                            'imgBadge' => '500GB Slim',
                            'overlayNote' => null,
                            'name' => 'PlayStation 3 (PS3 02)',
                            'desc' => 'PS3 Slim 500GB • 2 Stik Wireless • Full Game Siap Main plug & play.',
                            'chips' => ['Plug & Play', 'Full HDD Games'],
                            'ctaText' => 'Sewa Harian',
                            'ctaIcon' => 'bag',
                            'defaultSchedule' => 'now',
                            'searchText' => 'ps3 02 playstation 3 slim 500gb plug play full hdd games',
                        ],
                        [
                            'itemId' => 'daily-nintendo-01',
                            'tag' => 'NINTENDO 01',
                            'platformKey' => 'nintendo',
                            'statusKey' => 'available',
                            'statusLabel' => 'Tersedia',
                            'statusClass' => 'is-available',
                            'rate' => 120000,
                            'rateText' => 'Rp 120.000',
                            'image' => 'images/nintendo-unit.png',
                            'imgBadge' => 'OLED Screen',
                            'overlayNote' => null,
                            'name' => 'Nintendo Switch (Nintendo 01)',
                            'desc' => 'Nintendo Switch OLED 64GB • Dock + 2 Joy-Con • Mario Kart 8 Deluxe & Zelda BOTW.',
                            'chips' => ['Mario Kart 8', 'Zelda BOTW'],
                            'ctaText' => 'Sewa Harian',
                            'ctaIcon' => 'bag',
                            'defaultSchedule' => 'now',
                            'searchText' => 'nintendo 01 switch oled mario kart 8 zelda botw joy-con dock',
                        ],
                        [
                            'itemId' => 'daily-nintendo-02',
                            'tag' => 'NINTENDO 02',
                            'platformKey' => 'nintendo',
                            'statusKey' => 'in-use',
                            'statusLabel' => 'Sedang Disewa',
                            'statusClass' => 'is-rented',
                            'rate' => 110000,
                            'rateText' => 'Rp 110.000',
                            'image' => 'images/daily-nintendo-02.jpg',
                            'imgBadge' => null,
                            'overlayNote' => 'Kembali 27 Okt',
                            'name' => 'Nintendo Switch (Nintendo 02)',
                            'desc' => 'Nintendo Switch V2 Battery Improved • Carry Case • Smash Bros & Animal Crossing.',
                            'chips' => ['Smash Bros', 'Carry Case'],
                            'ctaText' => 'Sedang Disewa',
                            'ctaIcon' => 'lock',
                            'defaultSchedule' => 'tomorrow-09',
                            'searchText' => 'nintendo 02 switch v2 smash bros animal crossing carry case',
                        ],
                    ];
                @endphp

                <!-- GRID 4 KOLOM (DESKTOP - 3) -->
                <div class="ps-daily-grid">
                    @foreach ($dailyUnits as $unit)
                        <article
                            class="ps-daily-card {{ $unit['statusKey'] !== 'available' ? 'is-muted' : '' }}"
                            data-unit-card
                            data-price-mode="daily"
                            data-platform="{{ $unit['platformKey'] }}"
                            data-status="{{ $unit['statusKey'] }}"
                            data-rate="{{ $unit['rate'] }}"
                            data-search="{{ $unit['searchText'] }}"
                        >
                            <div class="ps-daily-card-top">
                                <span class="ps-daily-tag-pill">{{ $unit['tag'] }}</span>
                                <span class="ps-daily-status-pill {{ $unit['statusClass'] }}">
                                    <span class="ps-daily-status-dot"></span>
                                    {{ $unit['statusLabel'] }}
                                </span>
                            </div>

                            <div class="ps-daily-thumb">
                                <img src="{{ asset($unit['image']) }}" alt="{{ $unit['name'] }}" loading="lazy">
                                @if ($unit['imgBadge'])
                                    <span class="ps-daily-img-badge">{{ $unit['imgBadge'] }}</span>
                                @endif
                                @if ($unit['overlayNote'])
                                    <div class="ps-daily-thumb-overlay">
                                        <span>{{ $unit['overlayNote'] }}</span>
                                    </div>
                                @endif
                            </div>

                            <h3 class="ps-daily-card-title">{{ $unit['name'] }}</h3>
                            <p class="ps-daily-card-desc">{{ $unit['desc'] }}</p>

                            <div class="ps-daily-chips">
                                @foreach ($unit['chips'] as $chip)
                                    <span class="ps-daily-chip">{{ $chip }}</span>
                                @endforeach
                            </div>

                            <div class="ps-daily-card-foot">
                                <div class="ps-daily-price-row">
                                    <span class="ps-daily-price-label">Tarif Sewa</span>
                                    <strong class="ps-daily-price-val">{{ $unit['rateText'] }} <small>/ hari</small></strong>
                                </div>

                                <button
                                    type="button"
                                    class="ps-daily-cta {{ $unit['statusKey'] === 'available' ? 'is-primary' : 'is-secondary' }}"
                                    data-open-booking
                                    data-item-id="{{ $unit['itemId'] }}"
                                    data-default-service="express"
                                    data-default-duration="1d"
                                    data-default-schedule="{{ $unit['defaultSchedule'] }}"
                                    data-title="Sewa Harian {{ $unit['tag'] }}"
                                    data-subtitle="{{ $unit['name'] }} • {{ $unit['rateText'] }}/hari ({{ $unit['statusLabel'] }})"
                                >
                                    @if ($unit['ctaIcon'] === 'bag')
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                                            <path d="M3 6h18"/>
                                            <path d="M16 10a4 4 0 0 1-8 0"/>
                                        </svg>
                                    @elseif ($unit['ctaIcon'] === 'lock')
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                        </svg>
                                    @else
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76Z"/>
                                        </svg>
                                    @endif
                                    <span>{{ $unit['ctaText'] }}</span>
                                </button>
                            </div>
                        </article>
                    @endforeach

                    <div id="p2-empty" class="ps-empty-state" hidden>
                        <h3>Tidak Ada Unit Harian yang Cocok</h3>
                        <p>Coba ubah kata kunci pencarian atau reset filter untuk melihat kembali semua unit sewa harian.</p>
                        <button type="button" id="p2-reset-filters" class="ps-btn-primary">Reset Semua Filter</button>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('partials.ps-footer')
    @include('partials.ps-modals')
</body>
</html>
