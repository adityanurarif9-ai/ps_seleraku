<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Page Ke2 · Unit &amp; Game · PS Seleraku</title>
    <link rel="stylesheet" href="{{ asset('seleraku-pages.css') }}?v=logo-sync-1">
    <script src="{{ asset('seleraku-pages.js') }}?v=catalog-sync-2" defer></script>
@include('partials.pwa-head')
</head>
<body class="ps-page-body" data-reservation-url="{{ route('reservations.create') }}">
@include('partials.pwa-loading')
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
                        <option value="maintenance">Tidak tersedia</option>
                    </select>
                </div>

                <div class="ps-filter-meta">
                    <span id="p2-count">Ketersediaan untuk 1 jam ke depan · Muat ulang untuk memperbarui</span>
                    <div class="ps-legend-group">
                        <span class="ps-legend-item"><span class="ps-dot ps-dot-green"></span> Hijau: Siap Main</span>
                        <span class="ps-legend-item"><span class="ps-dot ps-dot-red"></span> Merah: Penuh pada rentang ini</span>
                        <span class="ps-legend-item"><span class="ps-dot ps-dot-yellow"></span> Jadwal akhir diperiksa saat reservasi</span>
                    </div>
                </div>
            </div>


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

                <div id="p2-empty" class="ps-empty-state" @if(count($classicUnits)+count($proUnits)>0) hidden @endif>
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
