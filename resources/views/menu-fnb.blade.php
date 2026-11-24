<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu Camilan &amp; Minuman Barista — PS SELERAKU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('seleraku-pages.css?v=logo-sync-1') }}">
    <script src="{{ asset('seleraku-pages.js?v=reservations-1') }}" defer></script>
</head>
<body class="ps-page-body" data-reservation-url="{{ route('reservations.create') }}">
    @include('partials.ps-navbar', ['activePage' => 'menu-fnb'])

    <main>
        <!-- HERO BANNER -->
        <section class="ps-hero-page2" aria-label="Hero Lounge F&B PS Seleraku">
            <div class="ps-hero-inner">
                <h1 class="ps-hero-title">
                    RENTAL <span class="ps-text-ps4">PS4</span> &amp; <span class="ps-text-ps5">PS5</span>
                </h1>
                <p class="ps-hero-subtitle">
                    Nikmati pengalaman gaming terbaik tanpa harus membeli. Lengkapi sesi mabar Anda dengan menu camilan renyah dan racikan minuman barista segar yang diantar langsung ke meja konsol.
                </p>
            </div>
        </section>

        <!-- LOUNGE F&B CATALOG (DESKTOP - 4) -->
        <section id="fnb-catalog" class="ps-fnb-section" aria-label="Menu Camilan dan Minuman Barista">
            <div class="ps-container">
                <div class="ps-fnb-header">
                    <div class="ps-fnb-header-left">
                        <div class="ps-fnb-eyebrow-row">
                            <span class="ps-fnb-pill-badge">LOUNGE REFRESHMENT</span>
                            <span class="ps-fnb-eyebrow">CONCIERGE F&amp;B DELIVERY</span>
                        </div>
                        <h2 class="ps-fnb-title">Menu Camilan &amp; Minuman Barista</h2>
                        <p class="ps-fnb-subtitle">
                            Pesan langsung dari sofa VIP konsol Anda tanpa menjeda aksi seru mabar. Diantar hangat langsung ke meja.
                        </p>
                    </div>

                    <div class="ps-fnb-search-wrap">
                        <svg class="ps-fnb-search-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                        <input
                            id="fnb-search"
                            type="search"
                            placeholder="Cari makanan, snack gurih, kopi, atau es..."
                            aria-label="Cari makanan, snack gurih, kopi, atau minuman"
                        >
                        <button id="fnb-search-clear" type="button" class="ps-fnb-clear-btn" aria-label="Hapus pencarian menu" hidden>&times;</button>
                    </div>
                </div>

                <!-- CATEGORY FILTER PILLS -->
                <div class="ps-fnb-filters" role="group" aria-label="Kategori Menu Lounge">
                    <button type="button" class="ps-fnb-cat-btn is-active" data-fnb-filter="all">Semua Menu</button>
                    <button type="button" class="ps-fnb-cat-btn" data-fnb-filter="makanan-berat">Makanan Berat</button>
                    <button type="button" class="ps-fnb-cat-btn" data-fnb-filter="camilan">Camilan &amp; Gorengan</button>
                    <button type="button" class="ps-fnb-cat-btn" data-fnb-filter="minuman">Minuman Dingin &amp; Kopi</button>
                    <button type="button" class="ps-fnb-cat-btn is-combo" data-fnb-filter="combo">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#f43f5e" stroke-width="2.2" aria-hidden="true">
                            <path d="M12 2L14.8 8.5L22 9.3L16.5 14L18.2 21L12 17.3L5.8 21L7.5 14L2 9.3L9.2 8.5L12 2Z"/>
                        </svg>
                        <span>Paket Combo Mabar</span>
                    </button>
                </div>

                <!-- SUB-BAR COUNT & KITCHEN INFO -->
                <div class="ps-fnb-subbar">
                    <div class="ps-fnb-subbar-left">
                        <h3>Katalog Pilihan Meja VIP</h3>
                        <span id="fnb-count" class="ps-fnb-count-badge">6 Pilihan Tersedia</span>
                    </div>
                    <div class="ps-fnb-subbar-right">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.2" aria-hidden="true">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>
                        <span>Dapur Lounge Higienis &amp; Freshly Cooked</span>
                    </div>
                </div>

                @php
                    $fnbItems = [
                        [
                            'id' => 'fnb-indomie',
                            'name' => 'Indomie Goreng Double + Telur Kornet',
                            'categories' => 'makanan-berat combo',
                            'badge' => 'Favorit Gamers',
                            'badgeClass' => 'is-crimson',
                            'rating' => '4.9',
                            'image' => 'images/fnb-indomie.jpg',
                            'desc' => 'Mi goreng jumbo dobel porsi dengan telur mata sapi setengah matang, kornet tumis gurih, dan taburan bawang renyah.',
                            'price' => 18000,
                            'priceText' => 'Rp 18.000',
                            'search' => 'indomie goreng double telur kornet mi mie gamers favorit makanan berat combo',
                        ],
                        [
                            'id' => 'fnb-kentang',
                            'name' => 'Kentang Goreng Truffle Crinkle',
                            'categories' => 'camilan',
                            'badge' => 'Gorengan Premium',
                            'badgeClass' => 'is-blue',
                            'rating' => null,
                            'image' => 'images/fnb-kentang.jpg',
                            'desc' => 'Kentang crinkle renyah dengan semprotan minyak truffle asli, taburan keju parmesan gurih dan garlic dip.',
                            'price' => 15000,
                            'priceText' => 'Rp 15.000',
                            'search' => 'kentang goreng truffle crinkle fries keju parmesan garlic dip camilan snack',
                        ],
                        [
                            'id' => 'fnb-nasgor',
                            'name' => 'Nasi Goreng Spesial Seleraku',
                            'categories' => 'makanan-berat',
                            'badge' => 'Paling Mengenyangkan',
                            'badgeClass' => 'is-dark',
                            'rating' => null,
                            'image' => 'images/fnb-nasgor.jpg',
                            'desc' => 'Nasi goreng rempah istimewa dengan suwiran ayam, sosis sapi panggang, telur dadar iris, dan kerupuk udang renyah.',
                            'price' => 28000,
                            'priceText' => 'Rp 28.000',
                            'search' => 'nasi goreng spesial seleraku ayam sosis telur kerupuk makanan berat',
                        ],
                        [
                            'id' => 'fnb-chicken',
                            'name' => 'Chicken Popcorn Spicy Mayo',
                            'categories' => 'camilan combo',
                            'badge' => 'Pedas Gurih',
                            'badgeClass' => 'is-crimson',
                            'rating' => null,
                            'image' => 'images/fnb-chicken.jpg',
                            'desc' => 'Daging paha ayam tanpa tulang renyah berbalut bumbu rahasia dengan cocolan saus spicy mayo pedas manis nagih.',
                            'price' => 22000,
                            'priceText' => 'Rp 22.000',
                            'search' => 'chicken popcorn spicy mayo ayam goreng renyah camilan pedas gurih combo',
                        ],
                        [
                            'id' => 'fnb-kopi',
                            'name' => 'Kopi Susu Gula Aren Barista',
                            'categories' => 'minuman combo',
                            'badge' => 'Fresh Brew Barista',
                            'badgeClass' => 'is-teal',
                            'rating' => null,
                            'image' => 'images/fnb-kopi.jpg',
                            'desc' => 'Double shot espresso arabika pilihan dipadukan susu segar pasteurisasi dan legitnya sirup gula aren organik.',
                            'price' => 16000,
                            'priceText' => 'Rp 16.000',
                            'search' => 'kopi susu gula aren barista es espresso arabika minuman dingin combo',
                        ],
                        [
                            'id' => 'fnb-esteh',
                            'name' => 'Es Teh Manis Jumbo Lemonade',
                            'categories' => 'minuman combo',
                            'badge' => 'Pelepas Dahaga',
                            'badgeClass' => 'is-emerald',
                            'rating' => null,
                            'image' => 'images/fnb-esteh.jpg',
                            'desc' => 'Seduhan teh melati wangi khas porsi disajikan dalam gelas jumbo 700ml dengan perasan jeruk lemon asli yang segar.',
                            'price' => 14000,
                            'priceText' => 'Rp 14.000',
                            'search' => 'es teh manis jumbo lemonade lemon melati segar minuman dingin combo',
                        ],
                    ];
                @endphp

                <!-- 3-COLUMN MENU GRID -->
                <div class="ps-fnb-grid">
                    @foreach ($fnbItems as $item)
                        <article
                            class="ps-fnb-card"
                            data-fnb-card
                            data-fnb-id="{{ $item['id'] }}"
                            data-fnb-name="{{ $item['name'] }}"
                            data-fnb-price="{{ $item['price'] }}"
                            data-fnb-categories="{{ $item['categories'] }}"
                            data-fnb-search="{{ $item['search'] }}"
                        >
                            <div class="ps-fnb-thumb">
                                <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" loading="lazy">
                                <span class="ps-fnb-tag {{ $item['badgeClass'] }}">{{ $item['badge'] }}</span>
                                @if ($item['rating'])
                                    <span class="ps-fnb-rating">
                                        <span class="ps-fnb-star" aria-hidden="true">&#9733;</span>
                                        {{ $item['rating'] }}
                                    </span>
                                @endif
                            </div>

                            <div class="ps-fnb-body">
                                <h3 class="ps-fnb-card-title">{{ $item['name'] }}</h3>
                                <p class="ps-fnb-card-desc">{{ $item['desc'] }}</p>

                                <div class="ps-fnb-card-foot">
                                    <div>
                                        <span class="ps-fnb-price-label">HARGA</span>
                                        <strong class="ps-fnb-price-val">{{ $item['priceText'] }}</strong>
                                    </div>

                                    <div class="ps-fnb-action-slot">
                                        <button
                                            type="button"
                                            class="ps-fnb-add-btn"
                                            data-fnb-add="{{ $item['id'] }}"
                                            aria-label="Tambah {{ $item['name'] }}"
                                        >
                                            + Tambah
                                        </button>

                                        <div class="ps-fnb-stepper" data-fnb-stepper="{{ $item['id'] }}" hidden>
                                            <button type="button" class="ps-fnb-step-btn is-minus" data-fnb-dec="{{ $item['id'] }}" aria-label="Kurangi {{ $item['name'] }}">&minus;</button>
                                            <span class="ps-fnb-step-qty" data-fnb-qty="{{ $item['id'] }}">0</span>
                                            <button type="button" class="ps-fnb-step-btn is-plus" data-fnb-inc="{{ $item['id'] }}" aria-label="Tambah porsi {{ $item['name'] }}">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach

                    <div id="fnb-empty" class="ps-empty-state" hidden>
                        <h3>Menu Tidak Ditemukan</h3>
                        <p>Tidak ada camilan atau minuman yang cocok dengan filter atau kata kunci Anda.</p>
                        <button type="button" id="fnb-reset-filters" class="ps-btn-primary">Tampilkan Semua Menu</button>
                    </div>
                </div>

                <!-- LIVE TABLE ORDER SUMMARY BAR (APPEARS WHEN ITEMS > 0) -->
                <div id="fnb-order-bar" class="ps-fnb-order-bar" hidden>
                    <div class="ps-fnb-order-info">
                        <span class="ps-fnb-order-badge" id="fnb-order-count">0 Item</span>
                        <div>
                            <strong id="fnb-order-summary">Belum ada item dipilih</strong>
                            <small id="fnb-order-items-preview">Tambahkan camilan atau minuman untuk diantar ke meja.</small>
                        </div>
                    </div>

                    <div class="ps-fnb-order-actions">
                        <select id="fnb-table-select" class="ps-fnb-table-select" aria-label="Pilih Meja atau Station VIP">
                            <option value="VIP-01 (PS5 Disc Ultra)">Meja VIP-01 (PS5 Ultra)</option>
                            <option value="VIP-03 (Party Room)">Meja VIP-03 (Party Room)</option>
                            <option value="REG-02 (PS5 Slim)">Station REG-02 (PS5 Slim)</option>
                            <option value="REG-04 (PS4 Pro)">Station REG-04 (PS4 Pro)</option>
                            <option value="Sofa Lounge Utama">Sofa Lounge Utama</option>
                        </select>
                        <strong class="ps-fnb-order-total" id="fnb-order-total">Rp 0</strong>
                        <button type="button" id="fnb-order-clear" class="ps-fnb-reset-btn">Kosongkan</button>
                        <button type="button" id="fnb-order-submit" class="ps-fnb-submit-btn">Kirim ke Meja &rarr;</button>
                    </div>
                </div>

                <!-- HYGIENE & WAITER CALLOUT CARD -->
                <div class="ps-fnb-hygiene-card">
                    <div class="ps-fnb-hygiene-left">
                        <div class="ps-fnb-hygiene-ico" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                                <path d="M9 12l2 2 4-4"/>
                                <path d="M12 3a9 9 0 1 0 9 9"/>
                                <path d="M18 3v4"/>
                                <path d="M16 5h4"/>
                            </svg>
                        </div>
                        <div>
                            <h4>Peralatan Higienis &amp; Controller Friendly</h4>
                            <p>Setiap pesanan camilan dilengkapi tissue basah pembersih stik controller antibakteri &amp; sendok garpu higienis.</p>
                        </div>
                    </div>

                    <div class="ps-fnb-hygiene-right">
                        <span>Butuh bantuan waiter?</span>
                        <button type="button" id="fnb-call-crew-btn" class="ps-fnb-crew-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M3 18v-6a9 9 0 0 1 18 0v6"/>
                                <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3z"/>
                                <path d="M3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>
                            </svg>
                            <span>Panggil Kru Lounge</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('partials.ps-footer')
    @include('partials.ps-modals')
</body>
</html>
