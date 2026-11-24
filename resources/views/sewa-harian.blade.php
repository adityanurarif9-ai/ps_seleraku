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
    <script src="{{ asset('seleraku-pages.js') }}?v=catalog-sync-2" defer></script>
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
                            <span id="p2-count">Ketersediaan untuk 24 jam ke depan · Muat ulang untuk memperbarui</span>
                        </div>
                        <div class="ps-daily-legend" aria-label="Legenda Status Unit">
                            <span><i class="ps-dot-indicator is-green"></i> Hijau: Siap Main</span>
                            <span><i class="ps-dot-indicator is-red"></i> Merah: Sedang Disewa</span>
                            <span><i class="ps-dot-indicator is-slate"></i> Pilih jadwal untuk memastikan unit</span>
                        </div>
                    </div>
                </div>


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

                    <div id="p2-empty" class="ps-empty-state" @if(count($dailyUnits)>0) hidden @endif>
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
