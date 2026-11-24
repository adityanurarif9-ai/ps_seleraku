<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Ke2 · Unit &amp; Game · PS Seleraku</title>
    <link rel="stylesheet" href="{{ asset('seleraku-pages.css') }}?v=logo-sync-1">
    <script src="{{ asset('seleraku-pages.js') }}?v=reservations-1?v=desktop-3-4-2" defer></script>
</head>
<body class="ps-page-body" data-reservation-url="{{ route('reservations.create') }}">
@include('partials.ps-navbar', ['activePage' => 'page-ke2'])

<main>
    <!-- Hero Section -->
    <section class="ps-hero-page2">
        <div class="ps-hero-inner">
            <h1 class="ps-hero-title">
                RENTAL <span class="ps-text-ps4">PS4</span> &amp; <span class="ps-text-ps5">PS5</span>
            </h1>
            <p class="ps-hero-subtitle">
                Nikmati pengalaman gaming terbaik tanpa harus membeli. Mainkan game favoritmu dengan koleksi terlengkap, fasilitas higienis prima, dan harga paling bersahabat.
            </p>
        </div>
    </section>

    <!-- Unit Catalog & Filter Section -->
    <section id="unit-catalog" class="ps-catalog-section">
        <div class="ps-container">
            <div class="ps-daily-head">
                <div>
                    <h2 class="ps-catalog-title">
                        <span class="cyan">PILIH PLAYSTATION</span>
                        <span class="white">FAVORITMU.</span>
                    </h2>
                    <p class="ps-catalog-desc">
                        Pilih konsol yang ingin kamu gunakan dan nikmati pengalaman bermain bersama teman dengan perangkat terawat, ruangan ber-AC dingin, display 4K HDR, dan koleksi game terlengkap terinstal siap main.
                    </p>
                </div>

                <nav class="ps-mode-switcher" aria-label="Mode Tarif Sewa">
                    <a href="{{ route('page-ke2') }}" class="ps-mode-tab is-active" aria-current="page">
                        <span>Main di Lounge</span>
                        <small>Per Jam</small>
                    </a>
                    <a href="{{ route('sewa-harian') }}" class="ps-mode-tab">
                        <span>Sewa Harian</span>
                        <small>Bawa Pulang</small>
                    </a>
                </nav>
            </div>

            <!-- Filter Panel -->
            <div class="ps-filter-panel">
                <div class="ps-filter-controls">
                    <label class="ps-search-box" aria-label="Cari konsol atau judul game">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input id="p2-search" type="search" placeholder="Cari konsol PlayStation atau judul game...">
                        <button id="p2-search-clear" type="button" class="ps-search-clear" hidden aria-label="Hapus pencarian">&times;</button>
                    </label>

                    <div class="ps-platform-group" role="group" aria-label="Filter Platform">
                        <button type="button" class="ps-platform-btn is-active" data-platform-filter="all">Semua</button>
                        <button type="button" class="ps-platform-btn" data-platform-filter="ps5">PlayStation 5</button>
                        <button type="button" class="ps-platform-btn" data-platform-filter="ps4">PlayStation 4</button>
                        <button type="button" class="ps-platform-btn" data-platform-filter="ps3">PlayStation 3</button>
                        <button type="button" class="ps-platform-btn" data-platform-filter="nintendo">Nintendo</button>
                    </div>

                    <select id="p2-price" class="ps-filter-select" aria-label="Filter Tarif">
                        <option value="all">Semua Tarif</option>
                        <option value="budget">&le; Rp 15.000 / jam</option>
                        <option value="mid">Rp 16.000 - Rp 25.000</option>
                        <option value="vip">&gt; Rp 25.000 / jam</option>
                    </select>

                    <select id="p2-status" class="ps-filter-select" aria-label="Filter Status">
                        <option value="all">Semua Status</option>
                        <option value="available">Tersedia</option>
                        <option value="in-use">Sedang Disewa / Digunakan</option>
                        <option value="booked">Sudah Dibooking</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>

                <div class="ps-filter-meta">
                    <span id="p2-count">Menampilkan 12 Unit Konsol (7 Siap Pakai)</span>
                    <div class="ps-legend-group">
                        <span class="ps-legend-item"><span class="ps-dot ps-dot-green"></span> Hijau: Siap Main</span>
                        <span class="ps-legend-item"><span class="ps-dot ps-dot-red"></span> Merah: Ada Sisa Waktu</span>
                        <span class="ps-legend-item"><span class="ps-dot ps-dot-yellow"></span> Kuning: Sudah Terjadwal</span>
                    </div>
                </div>
            </div>

            @php
                $classicUnits = [
                    [
                        'id' => 'ps3-01',
                        'platformKey' => 'ps3',
                        'platformLabel' => 'PLAYSTATION 3',
                        'statusKey' => 'available',
                        'statusLabel' => 'Tersedia',
                        'statusClass' => 'status-available',
                        'dotColor' => '#10b981',
                        'muted' => false,
                        'image' => 'images/ps3-unit.png',
                        'note' => null,
                        'noteType' => null,
                        'name' => 'PS3 01',
                        'rate' => 10000,
                        'rateText' => 'Rp 10.000',
                        'desc' => '500GB HDD • 2 Stik DualShock 3 Wireless • Full Game: PES/FIFA, GTA V, Tekken, Need for Speed.',
                        'specFull' => 'PlayStation 3 Slim 500GB HDD • 2 Stik DualShock 3 Wireless • HDMI Full HD 1080p',
                        'allGames' => ['eFootball PES 2024 Patch', 'FIFA 23 Legacy', 'GTA V', 'Tekken Tag Tournament 2', 'Need for Speed Most Wanted', 'God of War 3'],
                        'footerLabel' => 'Siap Main Langsung',
                        'footerIcon' => 'check',
                        'btnText' => 'Sewa Sekarang →',
                        'btnClass' => 'ps-btn-black',
                        'defaultSchedule' => 'now',
                    ],
                    [
                        'id' => 'ps3-02',
                        'platformKey' => 'ps3',
                        'platformLabel' => 'PLAYSTATION 3',
                        'statusKey' => 'in-use',
                        'statusLabel' => 'Sedang Disewa',
                        'statusClass' => 'status-rented',
                        'dotColor' => '#ef4444',
                        'muted' => true,
                        'image' => 'images/ps3-unit.png',
                        'note' => 'Estimasi selesai pk 16:30',
                        'noteType' => 'time',
                        'name' => 'PS3 02',
                        'rate' => 10000,
                        'rateText' => 'Rp 10.000',
                        'desc' => '500GB HDD • 2 Stik DualShock 3 Wireless • Full Game Collection (Action, Adventure, Racing).',
                        'specFull' => 'PlayStation 3 Slim 500GB HDD • 2 Stik DualShock 3 Wireless • Tersedia pre-order slot mulai pk 18:30 WIB',
                        'allGames' => ['GTA V', 'Uncharted 3', 'The Last of Us', 'Gran Turismo 6', 'PES 2024', 'Mortal Kombat Komplete'],
                        'footerLabel' => 'Sesi berjalan',
                        'footerIcon' => 'lock',
                        'btnText' => 'Pre-order Slot →',
                        'btnClass' => 'ps-btn-preorder-classic',
                        'defaultSchedule' => '18:30',
                    ],
                    [
                        'id' => 'ps3-03',
                        'platformKey' => 'ps3',
                        'platformLabel' => 'PLAYSTATION 3',
                        'statusKey' => 'maintenance',
                        'statusLabel' => 'Maintenance',
                        'statusClass' => 'status-maintenance',
                        'dotColor' => '#64748b',
                        'muted' => true,
                        'image' => 'images/ps3-unit.png',
                        'note' => 'Pengecekan rutin stik & sistem',
                        'noteType' => 'wrench',
                        'name' => 'PS3 03',
                        'rate' => 10000,
                        'rateText' => 'Rp 10.000',
                        'desc' => '500GB HDD • 2 Stik DualShock 3 Wireless • Full Game Retro & Sport series.',
                        'specFull' => 'PlayStation 3 Slim 500GB HDD • Sedang kalibrasi stik & update game • Bisa booking jadwal besok',
                        'allGames' => ['PES 2024 Retro', 'Winning Eleven', 'CTR Classic', 'Naruto Storm Revolution', 'NBA 2K', 'MotoGP'],
                        'footerLabel' => 'Kalibrasi stik',
                        'footerIcon' => 'sync',
                        'btnText' => 'Booking Jadwal →',
                        'btnClass' => 'ps-btn-preorder-classic',
                        'defaultSchedule' => 'tomorrow-09',
                    ],
                    [
                        'id' => 'nintendo-01',
                        'platformKey' => 'nintendo',
                        'platformLabel' => 'NINTENDO',
                        'statusKey' => 'available',
                        'statusLabel' => 'Tersedia',
                        'statusClass' => 'status-available',
                        'dotColor' => '#10b981',
                        'muted' => false,
                        'image' => 'images/nintendo-unit.png',
                        'note' => null,
                        'noteType' => null,
                        'name' => 'Nintendo 01',
                        'rate' => 20000,
                        'rateText' => 'Rp 20.000',
                        'desc' => 'Nintendo Switch OLED White • 2 Joy-Con + Pro Controller • Mario Kart 8, Zelda: Tears of the Kingdom, Smash Bros...',
                        'specFull' => 'Nintendo Switch OLED White 64GB • 2 Joy-Con + Pro Controller • TV Dock Mode & Handheld',
                        'allGames' => ['Mario Kart 8 Deluxe', 'Zelda: Tears of the Kingdom', 'Super Smash Bros Ultimate', 'Super Mario Bros Wonder', 'Splatoon 3'],
                        'footerLabel' => 'Siap Main Langsung',
                        'footerIcon' => 'check',
                        'btnText' => 'Sewa Sekarang →',
                        'btnClass' => 'ps-btn-black',
                        'defaultSchedule' => 'now',
                    ],
                    [
                        'id' => 'nintendo-02',
                        'platformKey' => 'nintendo',
                        'platformLabel' => 'NINTENDO',
                        'statusKey' => 'in-use',
                        'statusLabel' => 'Sedang Disewa',
                        'statusClass' => 'status-rented',
                        'dotColor' => '#ef4444',
                        'muted' => true,
                        'image' => 'images/nintendo-unit.png',
                        'note' => 'Estimasi selesai pk 17:00',
                        'noteType' => 'time',
                        'name' => 'Nintendo 02',
                        'rate' => 20000,
                        'rateText' => 'Rp 20.000',
                        'desc' => 'Nintendo Switch OLED • TV Dock Mode & Portable • Pokemon Scarlet/Violet, Overcooked All You Can Eat...',
                        'specFull' => 'Nintendo Switch OLED White • TV Dock Mode & Portable • Siap pre-order slot mulai pk 18:30 WIB',
                        'allGames' => ['Pokemon Scarlet & Violet', 'Overcooked All You Can Eat', 'It Takes Two', 'Nintendo Switch Sports', 'Animal Crossing'],
                        'footerLabel' => 'Sesi berjalan',
                        'footerIcon' => 'lock',
                        'btnText' => 'Pre-order Slot →',
                        'btnClass' => 'ps-btn-preorder-classic',
                        'defaultSchedule' => '18:30',
                    ],
                    [
                        'id' => 'nintendo-03',
                        'platformKey' => 'nintendo',
                        'platformLabel' => 'NINTENDO',
                        'statusKey' => 'available',
                        'statusLabel' => 'Tersedia',
                        'statusClass' => 'status-available',
                        'dotColor' => '#10b981',
                        'muted' => false,
                        'image' => 'images/nintendo-unit.png',
                        'note' => null,
                        'noteType' => null,
                        'name' => 'Nintendo 03',
                        'rate' => 20000,
                        'rateText' => 'Rp 20.000',
                        'desc' => 'Nintendo Switch OLED • 4 Joy-Con Party Edition • Mario Party Superstars, Just Dance 2024, Nintendo Switch...',
                        'specFull' => 'Nintendo Switch OLED • 4 Joy-Con Party Edition (Multiplayer 4 Orang) • TV Dock & Travel Case',
                        'allGames' => ['Mario Party Superstars', 'Just Dance 2024', 'Nintendo Switch Sports', 'Mario Kart 8 Deluxe', 'Overcooked! 2'],
                        'footerLabel' => 'Siap Main Langsung',
                        'footerIcon' => 'check',
                        'btnText' => 'Sewa Sekarang →',
                        'btnClass' => 'ps-btn-black',
                        'defaultSchedule' => 'now',
                    ],
                ];

                $proUnits = [
                    [
                        'id' => 'ps5-vip-01',
                        'platformKey' => 'ps5',
                        'code' => 'UNIT VIP-01',
                        'platformLabel' => 'PlayStation 5',
                        'statusKey' => 'available',
                        'statusLabel' => 'Tersedia',
                        'image' => 'images/ps5-ultra.png',
                        'name' => 'PS5 Disc Edition Ultra',
                        'spec' => '2 Unit DualSense Wireless (Haptic)',
                        'specFull' => 'PS5 Disc Edition Ultra • 4K 120Hz Ray Tracing • SSD Ultra High Speed • 2 Unit DualSense Wireless (Haptic)',
                        'gamesHeading' => 'GAME POPULER TERINSTAL:',
                        'tags' => ['FC 24', 'Spider-Man 2', 'Tekken 8', 'God of War'],
                        'moreTag' => '+31 Lainnya',
                        'allGames' => ['FC 24', 'Spider-Man 2', 'Tekken 8', 'God of War Ragnarok', 'Gran Turismo 7', 'Horizon Forbidden West', 'Final Fantasy XVI', 'Ghost of Tsushima Director Cut'],
                        'rate' => 25000,
                        'rateText' => 'Rp 25.000',
                        'ctaText' => 'Booking',
                        'ctaClass' => 'ps-btn-booking',
                        'ctaIcon' => 'calendar',
                    ],
                    [
                        'id' => 'ps5-reg-02',
                        'platformKey' => 'ps5',
                        'code' => 'UNIT REG-02',
                        'platformLabel' => 'PlayStation 5',
                        'statusKey' => 'in-use',
                        'statusLabel' => 'Sedang Digunakan (Estimasi selesai: 1 jam 15 mnt lagi)',
                        'image' => 'images/ps5-slim.png',
                        'name' => 'PS5 Digital Slim Edition',
                        'spec' => '2 Unit DualSense (White & Midnight Black)',
                        'specFull' => 'PS5 Digital Slim Edition • 4K HDR Display • 2 Unit DualSense (White & Midnight Black)',
                        'gamesHeading' => 'GAME POPULER TERINSTAL:',
                        'tags' => ['FC 24', 'MK 1', 'NBA 2K24', 'Hogwarts Legacy'],
                        'moreTag' => '+28 Lainnya',
                        'allGames' => ['FC 24', 'Mortal Kombat 1', 'NBA 2K24', 'Hogwarts Legacy', 'Astro Playroom', 'Cyberpunk 2077', 'Elden Ring'],
                        'rate' => 22000,
                        'rateText' => 'Rp 22.000',
                        'ctaText' => 'Pre-order Slot',
                        'ctaClass' => 'ps-btn-preorder',
                        'ctaIcon' => 'clock',
                    ],
                    [
                        'id' => 'ps5-vip-03',
                        'platformKey' => 'ps5',
                        'code' => 'UNIT VIP-03',
                        'platformLabel' => 'PlayStation 5 VIP',
                        'statusKey' => 'booked',
                        'statusLabel' => 'Sudah Dibooking (Terjadwal pk 19:00 WIB - Slot Siang Ready)',
                        'image' => 'images/ps5-vip.png',
                        'name' => 'PS5 Party 4-Player VIP',
                        'spec' => '4 Stik DualSense • Sofa Private 4–6 Orang',
                        'specFull' => 'VIP Private Room & Soundproof • 4 Stik DualSense • Sofa Private 4–6 Orang • Display 65" 4K OLED',
                        'gamesHeading' => 'GAME CO-OP & PARTY FAVORIT:',
                        'tags' => ['Overcooked!', 'It Takes Two', 'FC 24', 'WWE 2K24'],
                        'moreTag' => '+40 Game',
                        'allGames' => ['Overcooked! All You Can Eat', 'It Takes Two', 'FC 24', 'WWE 2K24', 'Crash Team Racing', 'Street Fighter 6', 'Sackboy Big Adventure'],
                        'rate' => 35000,
                        'rateText' => 'Rp 35.000',
                        'ctaText' => 'Pilih Jam Lain',
                        'ctaClass' => 'ps-btn-booking',
                        'ctaIcon' => 'calendar',
                    ],
                    [
                        'id' => 'ps4-reg-04',
                        'platformKey' => 'ps4',
                        'code' => 'UNIT REG-04',
                        'platformLabel' => 'PlayStation 4 Pro',
                        'statusKey' => 'available',
                        'statusLabel' => 'Tersedia',
                        'image' => 'images/ps4-pro.png',
                        'name' => 'PS4 Pro 1TB 4K Enhanced',
                        'spec' => '2 Unit DualShock 4 Wireless Original',
                        'specFull' => 'PS4 Pro 1TB • Dynamic 4K Boost Mode • HDR Gaming • 2 Unit DualShock 4 Wireless Original',
                        'gamesHeading' => 'GAME POPULER TERINSTAL:',
                        'tags' => ['PES 2024 Patch', 'RDR 2', 'FIFA 23', 'Ghost Tsushima'],
                        'moreTag' => '+50 Game',
                        'allGames' => ['PES 2024 Patch Terbaru', 'Red Dead Redemption 2', 'FIFA 23', 'Ghost of Tsushima', 'Spider-Man', 'The Last of Us Part II', 'Uncharted 4'],
                        'rate' => 15000,
                        'rateText' => 'Rp 15.000',
                        'ctaText' => 'Booking',
                        'ctaClass' => 'ps-btn-booking',
                        'ctaIcon' => 'calendar',
                    ],
                    [
                        'id' => 'ps4-reg-05',
                        'platformKey' => 'ps4',
                        'code' => 'UNIT REG-05',
                        'platformLabel' => 'PlayStation 4 Slim',
                        'statusKey' => 'available',
                        'statusLabel' => 'Tersedia',
                        'image' => 'images/ps4-slim.png',
                        'name' => 'PS4 Slim 500GB Standard',
                        'spec' => '2 Unit DualShock 4 Wireless',
                        'specFull' => 'PS4 Slim 500GB • Silent Cooling • Full HD 1080p • Low Latency • 2 Unit DualShock 4 Wireless',
                        'gamesHeading' => 'GAME POPULER TERINSTAL:',
                        'tags' => ['GTA V', 'MK 11', 'NFS Heat', 'eFootball PES'],
                        'moreTag' => '+25 Game',
                        'allGames' => ['GTA V', 'Mortal Kombat 11', 'Need for Speed Heat', 'eFootball PES', 'Tekken 7', 'God of War'],
                        'rate' => 12000,
                        'rateText' => 'Rp 12.000',
                        'ctaText' => 'Booking',
                        'ctaClass' => 'ps-btn-booking',
                        'ctaIcon' => 'calendar',
                    ],
                    [
                        'id' => 'ps4-reg-06',
                        'platformKey' => 'ps4',
                        'code' => 'UNIT REG-06',
                        'platformLabel' => 'PlayStation 4 Fat',
                        'statusKey' => 'in-use',
                        'statusLabel' => 'Sedang Digunakan',
                        'image' => 'images/ps4-fat.png',
                        'name' => 'PS4 Standard Classic Unit',
                        'spec' => '2 Unit DualShock 4',
                        'specFull' => 'PS4 Standard Classic Unit • 1080p Full HD • DualShock 4 • TV LED 43"',
                        'gamesHeading' => 'GAME POPULER TERINSTAL:',
                        'tags' => ['PES 2024', 'Naruto Storm 4', 'CTR Nitro-Fueled'],
                        'moreTag' => '+20 Game',
                        'allGames' => ['PES 2024', 'Naruto Ultimate Ninja Storm 4', 'CTR Nitro-Fueled', 'GTA V', 'FIFA 23', 'Minecraft'],
                        'rate' => 10000,
                        'rateText' => 'Rp 10.000',
                        'ctaText' => 'Pre-order Slot',
                        'ctaClass' => 'ps-btn-preorder',
                        'ctaIcon' => 'clock',
                    ],
                ];
            @endphp

            <div class="ps-units-grid">
                @foreach($classicUnits as $unit)
                    <article
                        class="ps-unit-card-light {{ $unit['muted'] ? 'is-muted' : '' }}"
                        data-unit-card
                        data-platform="{{ $unit['platformKey'] }}"
                        data-status="{{ $unit['statusKey'] }}"
                        data-rate="{{ $unit['rate'] }}"
                        data-search="{{ strtolower($unit['platformKey'].' '.$unit['platformLabel'].' '.$unit['name'].' '.$unit['desc'].' '.$unit['statusLabel'].' '.implode(' ', $unit['allGames'])) }}"
                    >
                        <div class="ps-card-top-meta">
                            <span class="ps-platform-tag">{{ $unit['platformLabel'] }}</span>
                            <span class="ps-status-pill {{ $unit['statusClass'] }}">
                                <span class="ps-dot" style="background: {{ $unit['dotColor'] }};"></span>
                                {{ $unit['statusLabel'] }}
                            </span>
                        </div>

                        <div class="ps-thumb-box">
                            <img src="{{ asset($unit['image']) }}" alt="{{ $unit['name'] }}">
                            @if($unit['note'])
                                <span class="ps-thumb-note {{ $unit['noteType'] === 'time' ? 'is-red' : '' }}">
                                    @if($unit['noteType'] === 'time')
                                        &#9201;
                                    @else
                                        &#128295;
                                    @endif
                                    {{ $unit['note'] }}
                                </span>
                            @endif
                        </div>

                        <div class="ps-unit-head">
                            <h3>{{ $unit['name'] }}</h3>
                            <span class="ps-unit-rate">{{ $unit['rateText'] }} <small>/ jam</small></span>
                        </div>

                        <p class="ps-unit-desc">{{ $unit['desc'] }}</p>

                        <div class="ps-unit-footer">
                            <span class="ps-ready-label {{ $unit['footerIcon'] !== 'check' ? 'is-muted' : '' }}">
                                @if($unit['footerIcon'] === 'check')
                                    &#10004;
                                @elseif($unit['footerIcon'] === 'lock')
                                    &#128274;
                                @else
                                    &#8635;
                                @endif
                                {{ $unit['footerLabel'] }}
                            </span>

                            <div class="ps-classic-actions">
                                <button
                                    type="button"
                                    class="ps-btn-detail-mini"
                                    data-open-detail
                                    data-item-id="{{ $unit['id'] }}"
                                    data-name="{{ $unit['name'] }}"
                                    data-code="{{ $unit['platformLabel'] }}"
                                    data-platform="{{ $unit['platformLabel'] }}"
                                    data-status-label="{{ $unit['statusLabel'] }}"
                                    data-specs="{{ $unit['specFull'] }}"
                                    data-games="{{ implode('|', $unit['allGames']) }}"
                                    data-rate="{{ $unit['rate'] }}"
                                >
                                    Detail
                                </button>
                                <button
                                    type="button"
                                    class="{{ $unit['btnClass'] }}"
                                    data-open-booking
                                    data-item-id="{{ $unit['id'] }}"
                                    data-default-schedule="{{ $unit['defaultSchedule'] }}"
                                    data-title="Reservasi {{ $unit['name'] }}"
                                    data-subtitle="{{ $unit['platformLabel'] }} • {{ $unit['rateText'] }} / jam"
                                >
                                    {{ $unit['btnText'] }}
                                </button>
                            </div>
                        </div>
                    </article>
                @endforeach

                @foreach($proUnits as $unit)
                    <article
                        class="ps-unit-card-pro"
                        data-unit-card
                        data-platform="{{ $unit['platformKey'] }}"
                        data-status="{{ $unit['statusKey'] }}"
                        data-rate="{{ $unit['rate'] }}"
                        data-search="{{ strtolower($unit['platformKey'].' '.$unit['code'].' '.$unit['platformLabel'].' '.$unit['name'].' '.$unit['spec'].' '.implode(' ', $unit['allGames'])) }}"
                    >
                        <div class="ps-pro-banner">
                            <img src="{{ asset($unit['image']) }}" alt="{{ $unit['name'] }}">
                        </div>

                        <div class="ps-pro-body">
                            <h3 class="ps-pro-title">{{ $unit['name'] }}</h3>
                            <div class="ps-pro-spec">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="2" y="6" width="20" height="12" rx="6"/>
                                    <line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/>
                                    <circle cx="15" cy="13" r="1"/><circle cx="18" cy="11" r="1"/>
                                </svg>
                                <span>{{ $unit['spec'] }}</span>
                            </div>

                            <div class="ps-pro-games-label">{{ $unit['gamesHeading'] }}</div>
                            <div class="ps-game-pills">
                                @foreach($unit['tags'] as $tag)
                                    <span class="ps-game-pill">{{ $tag }}</span>
                                @endforeach
                                <span class="ps-game-pill is-more">{{ $unit['moreTag'] }}</span>
                            </div>

                            <div class="ps-pro-price-row">
                                <span>Tarif Rental</span>
                                <div class="ps-pro-price-val">{{ $unit['rateText'] }} <small>/ jam</small></div>
                            </div>

                            <div class="ps-pro-actions">
                                <button
                                    type="button"
                                    class="ps-btn-outline"
                                    data-open-detail
                                    data-item-id="{{ $unit['id'] }}"
                                    data-name="{{ $unit['name'] }}"
                                    data-code="{{ $unit['code'] }}"
                                    data-platform="{{ $unit['platformLabel'] }}"
                                    data-status-label="{{ $unit['statusLabel'] }}"
                                    data-specs="{{ $unit['specFull'] }}"
                                    data-games="{{ implode('|', $unit['allGames']) }}"
                                    data-rate="{{ $unit['rate'] }}"
                                >
                                    &#9432; Lihat Detail
                                </button>
                                <button
                                    type="button"
                                    class="{{ $unit['ctaClass'] }}"
                                    data-open-booking
                                    data-item-id="{{ $unit['id'] }}"
                                    data-title="{{ $unit['ctaText'] }} — {{ $unit['name'] }}"
                                    data-subtitle="{{ $unit['code'] }} • {{ $unit['statusLabel'] }}"
                                >
                                    {{ $unit['ctaText'] }}
                                </button>
                            </div>
                        </div>
                    </article>
                @endforeach

                <div id="p2-empty" class="ps-empty-state" hidden>
                    <h3 style="margin:0 0 8px;font-size:20px;">Tidak ada unit yang cocok dengan filter</h3>
                    <p style="margin:0 0 16px;color:#94a3b8;font-size:13.5px;">Coba ubah kata kunci pencarian atau reset filter untuk melihat seluruh konsol.</p>
                    <button id="p2-reset-filters" type="button" class="ps-nav-cta" style="border-radius:8px;">Reset Semua Filter</button>
                </div>
            </div>
        </div>
    </section>
</main>

@include('partials.ps-footer')
@include('partials.ps-modals')
</body>
</html>
